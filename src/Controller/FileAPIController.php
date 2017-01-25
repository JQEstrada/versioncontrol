<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Datasource\ConnectionManager;
use ftpal\ftpal;
use ZipArchive;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Cake\Utility\Text;

/**
 * FileAPIChanges Controller
 *
 * @property \App\Model\Table\TempfileChangesTable $TempfileChanges
 */
class FileAPIController extends AppController
{

    /********************************************************
    *********************API METHODS*************************
    ********************************************************/

    /*
    * Get Files to Push
    * Returns a random string with a given length
    * @param (optional) len: length of the return string
    * @returns json
    */
    public function getFilesToPushAjax($id = null)
    {
        //Prepare ajax data
        $this->autoRender = false;
        $this->RequestHandler->respondAs('json');
        $this->response->type('application/json');  
        ini_set('max_execution_time', 300); //300 seconds = 5 minutes
        $start = microtime(true);
        $response = array("type"=>"error", "message"=>"Error fetching files to push.", "filesToPush"=>"");
        $this->loadModel('Projects');
        $this->loadModel('Files');

        //Get Project
        $project = $this->Projects->get($id);

        $projectFilesToPush = $this->Files->find('all')->where(['Files.project_id' => $project->id, 'Files.pushed' => 0]);

        $response["type"] = "success";
        $response["message"] = "There are files to push to the remote server.";
        $filesToPush = $projectFilesToPush->toArray();

        if(!count($filesToPush)) { $response["message"] = "There are no files to push."; }
        $response["duration"] = microtime(true) - $start;
        $response["files"] = $filesToPush;
        $response["filesToPush"] = count($filesToPush);

        $this->RequestHandler->respondAs('json');
        $this->response->type('application/json');  
        echo json_encode($response);
    }


    /*
    * Get Changes
    * Checks whether there were changes made to the project files
    * @param id: Project ID
    * @returns json with operation info
    */
    public function getChangesAjax($id = null)
    {
        //Prepare ajax data
        $this->autoRender = false;
        $this->RequestHandler->respondAs('json');
        $this->response->type('application/json');  
        ini_set('max_execution_time', 300); //300 seconds = 5 minutes
        $start = microtime(true);
        $response = array("type"=>"error", "message"=>"Error fetching changes.", "modified"=>"", "added"=>"", "deleted"=>"");
        $added=0;
        $modified=0;
        $deleted=0;
        $this->loadModel('Projects');
        $this->loadModel('Tempfiles');

        //Get Project
        $project = $this->Projects->get($id,[ 'contain' => 'Versions']);
        $response["projectPath"] = $project->root_folder_path;
        $excludeTemp = $project->tmp_folder_path;

        // Get current files
        $fileRecords = $this->getCurrentFiles($project->id, $project->archive_folder_path);

        $conn = ConnectionManager::get('default');
        $truncateResult = $conn->execute('TRUNCATE TABLE tempfiles'); 
        $tempfiles = array();

        if ($this->Tempfiles->saveMany($fileRecords)) {

            $this->loadModel('Procedures');
            $procedure = $this->Procedures->newEntity();
            $procedure->current_procedure = "CommitChanges";
            $handshake = $this->incrementalHash();
            $procedure->handshake_code = $handshake;
            $procedure->project_id = $project->id;

            if ($procedure = $this->Procedures->save($procedure)) {

                $modifiedFiles = $this->getChanges($project->id); // Get all modifications to files

                foreach ($modifiedFiles as $modifiedFile) {

                    // Prepare new file changes to be saved
                    $this->loadModel('TempfileChanges');
                    $tempFileChange = $this->TempfileChanges->newEntity();


                    $tempFileChange->path = $modifiedFile["filePath"];

                    // If temporary folder marked to be excluded and file is in temporary folder
                    // don't consider this file
                    if($excludeTemp && $this->isInPath($modifiedFile['filePath'], $excludeTemp) ) {
                        continue;
                    }

                    $tempFileChange->fileid = $modifiedFile["fileId"];
                    $tempFileChange->action = $modifiedFile["changeType"];
                    $tempFileChange->procedure_id = $procedure->id;
                    $tempFileChange->name = $modifiedFile["fileName"];
                    $tempFileChange->type = $modifiedFile["fileType"];
                    $tempFileChange->extension = $modifiedFile["fileExtension"];
                    $tempFileChange->size = $modifiedFile["fileSize"];
                    $tempFileChange->lastModified = $modifiedFile["fileLastModified"];
                    $tempFileChange->topval = $modifiedFile["TopVal"];

                    // If file was successfuly copied
                    if ($generatedFile = $this->TempfileChanges->save($tempFileChange)) {
                        $added++; 
                        array_push($tempfiles, $tempFileChange);
                    } else {
                        $response["message"] = 'The new file could not be registered.';
                        $response["errors"] = $tempFileChange->errors();
                        echo json_encode($response);
                        die();
                    }

                }

            } 

        }


        $response["type"] = "success";
        $response["message"] = "There were changes made to project.";
        if(!count($tempfiles)) { $response["message"] = "There were no changes made to the project."; }
        $response["duration"] = microtime(true) - $start;
        $response["added"] = count($modifiedFiles);
        $response["modified"] = $tempfiles;
        $response["deleted"] = $deleted;
        $response["totalChanged"] = $added + $modified + $deleted;
        $response["handshake_code"] = $handshake;

        $this->RequestHandler->respondAs('json');
        $this->response->type('application/json');  
        echo json_encode($response);
    }



    /*
    * Confirm Commit
    * Commits file modifications to repository with option to update remote server
    * @param id: Project ID
    * @returns json with operation info
    */
    public function confirmCommitAjax($id = null)
    {
        //Prepare ajax data
        $this->autoRender = false;
        $this->RequestHandler->respondAs('json');
        $this->response->type('application/json');  
        ini_set('max_execution_time', 300); //300 seconds = 5 minutes
        $start = microtime(true);
        $response = array("type"=>"error", "message"=>"Error commiting.");
        $this->loadModel('Procedures');
        $added=0;
        $modified=0;
        $deleted=0;


        $handshake = $id;
        $procedure = $this->Procedures->find('all', [
              'conditions' => ['handshake_code' => $handshake]
          ])->first();
        $options = $this->request->input('json_decode', true);
        $selectedFiles = $options["selectedFiles"];
        $pushFiles = $options["pushFiles"];
        $comments = $options["comments"];

        if($id && $selectedFiles) {

            $this->loadModel('Versions');
            $this->loadModel('Projects');
            $this->loadModel('Files');
            $this->loadModel('FileChanges');
            $project = $this->Projects->get($procedure->project_id,[ 'contain' => 'Versions']);
            $fixedFolderName = strtolower(str_replace(' ', '_' , $project->name));
            $structure = './project_sources/'.$fixedFolderName;
            $archive = $structure."/archive";

            $filesFilter = implode(",", $selectedFiles);
            $filesToCommit = array();

            $conn = ConnectionManager::get('default');
            $queryChanges = $conn->execute('SELECT `id`, `fileid`, `action`, `name`, `path`, `type`, `size`, `extension`, `lastModified`, `topval` FROM tempfile_changes WHERE procedure_id = "'.$procedure->id.'" AND fileid IN ('.$filesFilter.')'); 
            $tempFileChanges = $queryChanges->fetchAll('assoc');

            
            //Create New Version

            $newVersion = $this->Versions->newEntity();
            $newVersion->project_id = $project->id;
            $newVersion->comments = $comments;
            $howManyVersions = count($project->versions);
            $oldVersion =  $project->versions[$howManyVersions-1];
            $oldVersion->is_current_version = "no";
            $newVersion->version_number = $oldVersion->version_number + 1;
            if (!$this->Versions->saveMany(array($oldVersion,$newVersion))) {
                $response["message"] = 'Version could not be created.';
                echo json_encode($response);
                die();
            }
            //Create New Version - END

            $newFileChanges = array();
            $updatedFiles = array();
            $deletedFiles = array();

            foreach($tempFileChanges as $fileChange) {

                if($fileChange["action"] == "added") {
                    // Save each new file in file db and folder
                    $newFile = $this->Files->newEntity();              
                    $newFile->name = $fileChange["name"];
                    $newFile->project_id = $project->id;
                    $newFile->path = $fileChange["path"];
                    $newFile->type = $fileChange["type"];
                    $newFile->extension = $fileChange["extension"];
                    $newFile->size = $fileChange["size"];
                    $newFile->last_modified = $fileChange["lastModified"];

                    // Copy the added file to repository
                    $completeFileName = $fileChange['name'].".".$fileChange['extension'];
                    $filePath = $fileChange["path"]."\\".$completeFileName;
                    $repositoryPath = str_replace('file:///'.$project->root_folder_path, $structure, $fileChange["path"]);
                    $fileToCopy = $fileChange["path"] ."\\".$completeFileName;
                    $repositoryNewFile = $repositoryPath."\\".$completeFileName;
                    @mkdir($repositoryPath, 0777, true); // Create repository directory

                    if(!copy($fileToCopy, $repositoryNewFile)){
                        $response["message"] = 'File could not be created on repository.';
                        echo json_encode($response);
                        die();
                    }
                    else {
                        // If file was successfuly copied
                        if ($generatedFile = $this->Files->save($newFile)) {
                            $added++; 
                            $fileId = $generatedFile->id;      
                            array_push($updatedFiles, $generatedFile);
                        } else {
                            $response["message"] = 'The new file could not be created.';
                            echo json_encode($response);
                            die();
                        }
                    }
                }


                // If file is being updated or deleted,create a copy to new archive folder. 
                // In case of updated (modified) files, update the file on repository
                if($fileChange["action"] == "modified" || $fileChange["action"] == "deleted") {
                    
                    $typeOfAction = $fileChange["action"];     
                    $fileId = $fileChange["fileid"];                   

                    $newArchive = $archive."/version_".$newVersion->version_number;
                    $newArchivePath = str_replace('file:///'.$project->root_folder_path, $newArchive, $fileChange["path"]);
                    @mkdir($newArchivePath, 0777, true); // Create archive directory
                    $completeFileName = $fileChange['name'].".".$fileChange['extension'];
                    $fileToCopy = $fileChange['path']."\\".$completeFileName;
                    $moveFile = false;


                    if($typeOfAction == "modified"){
                        $isDeleted = 0;
                        $moveFile = copy($fileToCopy, $newArchivePath."\\".$completeFileName);

                    }
                    else { //Deleted 
                        $repositoryPath = str_replace('file:///'.$project->root_folder_path, $structure, $fileChange["path"]);
                        $fileToCopy = $repositoryPath ."\\".$completeFileName;
                        $isDeleted = 1;
                        $moveFile = rename($fileToCopy, $newArchivePath."\\".$completeFileName);
                    }

                    if(!$moveFile){
                        $response["message"] = 'File could not be archived.';
                        echo json_encode($response);
                        die();
                    }
                    else {


                        if($typeOfAction == "modified"){
                            // Replace the modified file to repository
                            $filePath = $fileChange["path"]."\\".$completeFileName;
                            $repositoryPath = str_replace('file:///'.$project->root_folder_path, $structure, $fileChange["path"]);
                            $repositoryNewFile = $repositoryPath."\\".$completeFileName;

                            if(!copy($fileToCopy, $repositoryNewFile)){
                                $response["message"] = "File could not be replaced on repository.";
                                echo json_encode($response);
                                die();
                            }

                        }

                        $updatedFile = $this->Files->get($fileId); 

                        if($typeOfAction == "modified"){ 
                            array_push($updatedFiles, $updatedFile);
                        }

                        if($typeOfAction == "deleted"){ 
                            array_push($deletedFiles, $updatedFile);
                        }

                        
                        $updatedFile->last_modified = $fileChange["topval"];
                        $updatedFile->deleted = $isDeleted;
                        $updatedFile->pushed = 0;

                        if ($this->Files->save($updatedFile)) {
                            if($typeOfAction=="modified") { $modified++; } else { $deleted++; }     
                        } else {
                            $response["message"] = "The modified file could not be updated.";
                            echo json_encode($response);
                            die();
                        }
                    }

                } 

                // Prepare new file change to be saved
                $newFileChange = $this->FileChanges->newEntity();
                
                $newFileChange->file_id = $fileId;
                $newFileChange->version_id = $newVersion->id;
                $newFileChange->action = $fileChange["action"];

                array_push($newFileChanges, $newFileChange);

            }

            // Commit file changes into file changes table
            $fileChangesAbsent = empty($newFileChanges);
            if ($fileChangesAbsent) {
                if(!$this->Versions->delete($newVersion)) {
                    $response["message"] = "Version error.";
                    echo json_encode($response);
                    die();
                }
                $response["message"] = "No changes to be made";
            } else if(!$this->FileChanges->saveMany($newFileChanges)){
                $response["message"] = "File changes error.";
                echo json_encode($response);
                die();
            }


            if($pushFiles) {

                if(count($updatedFiles)) {
                    $filesSent = $this->sendFTP($project, $updatedFiles, $options['ftpServer'], $options['ftpUsername'], $options['ftpPassword']);
                    $response["filesSent"] = $filesSent;
                }
                if(count($deletedFiles)) {
                    $filesDeleted = $this->deleteFTP($project, $deletedFiles, $options['ftpServer'], $options['ftpUsername'], $options['ftpPassword']);
                    $response["filesDeleted"] = $filesDeleted;
                }                

            }
            
            
            $response["filesToCommit"] = $filesToCommit;
            $response["type"] = "success";
            $response["message"] = "Selected files were commited to server.";
            $response["duration"] = microtime(true) - $start;
            $response["handshake_code"] = $handshake;
            $response["allTempFiles"] = array("{teste: teste}");

        }



        $this->RequestHandler->respondAs('json');
        $this->response->type('application/json');  
        echo json_encode($response);

    }



    /*
    * Push File List
    * Pushes file modifications to remote server
    * @param id: Project ID
    * @returns json with operation info
    */
    public function pushFileListAjax($id = null)
    {
        //Prepare ajax data
        $this->autoRender = false;
        $this->RequestHandler->respondAs('json');
        $this->response->type('application/json');  
        ini_set('max_execution_time', 300); //300 seconds = 5 minutes
        $start = microtime(true);
        $response = array("type"=>"error", "message"=>"Error fetching files to push.", "filesToPush"=>"");
        $this->loadModel('Projects');
        $this->loadModel('Files');

        // Get post data
        $options = $this->request->input('json_decode', true);

        // Get Project
        $project = $this->Projects->get($id);

        // Get Files
        $selectedFiles = $options["selectedFiles"];
        $updatedFiles = array();
        $deletedFiles = array();
        $markFilesPushed = array();

        $projectFilesToPush = $this->Files->find('all')->where(['Files.project_id' => $project->id, 'Files.pushed' => 0]);

        foreach($projectFilesToPush as $projectFile) {

            if(!$projectFile->pushed){

                $projectFile->pushed = 1;

                array_push($markFilesPushed, $projectFile);

                if($projectFile->deleted) { 

                    array_push($deletedFiles, $projectFile); 

                } else {

                    array_push($updatedFiles, $projectFile);
                    
                }

            }


        }


        if(count($updatedFiles)) {
            $filesSent = $this->sendFTP($project, $updatedFiles, $options['ftpServer'], $options['ftpUsername'], $options['ftpPassword']);
            $response["filesSent"] = $filesSent;
        }
        if(count($deletedFiles)) {
            $filesDeleted = $this->deleteFTP($project, $deletedFiles, $options['ftpServer'], $options['ftpUsername'], $options['ftpPassword']);
            $response["filesDeleted"] = $filesDeleted;
        }   

        $filesMarkedPush = $this->Files->saveMany($markFilesPushed);



        $response["type"] = "success";
        $response["message"] = "There are files to push to the remote server.";
        if(!count($selectedFiles)) { $response["message"] = "There are no files to push."; }
        $response["duration"] = microtime(true) - $start;
        $response["files"] = $selectedFiles;
        $response["filesToPush"] = count($selectedFiles);
        $response["filesMarkedPush"] = $filesMarkedPush;

        $this->RequestHandler->respondAs('json');
        $this->response->type('application/json');  
        echo json_encode($response);
    }




    /*
    * Ignore All
    * Marks all commited files as pushed
    * @param id: Project ID
    * @returns json with operation info
    */
    public function ignoreAllAjax($id = null)
    {

        //Prepare ajax data
        $this->autoRender = false;
        $this->RequestHandler->respondAs('json');
        $this->response->type('application/json');  
        ini_set('max_execution_time', 300); //300 seconds = 5 minutes
        $start = microtime(true);
        $response = array("type"=>"error", "message"=>"Error marking files as pushed.", "modified"=>"");
        $modified=0;
        $this->loadModel('Projects');
        $this->loadModel('Files');

        //Get Project
        $project = $this->Projects->get($id);

        // Get current files
        $fileRecords = $this->Files->find('all')->where(["Files.project_id" => $project->id, "Files.pushed" => 0]);
        
        if(!count($fileRecords)) {

            $response["message"] = 'No files to be pushed.';
            echo json_encode($response);
            die();

        }


        foreach ($fileRecords as $fileRecord) {

            $fileRecord->pushed = 1;

        }

        if($this->Files->saveMany($fileRecords)){

            $modified = count($fileRecords);

        } else {

            $response["message"] = 'Could not mark files as pushed.';
            echo json_encode($response);
            die();

        }

        $response["type"] = "success";
        $response["message"] = "All project files are marked as pushed.";
        $response["duration"] = microtime(true) - $start;
        $response["modified"] = $modified;

        $this->RequestHandler->respondAs('json');
        $this->response->type('application/json');  
        echo json_encode($response);
    }




    /*
    * Process
    * Starts and continues a new project creation process
    * based on an handshake string received. If handshake is null
    * a new process is initiated
    * @returns json with operation info
    */
    public function process(){

      $this->loadModel('Procedures');
      $this->loadModel('Projects');
      $this->autoRender = false;
      $this->RequestHandler->respondAs('json');
      $this->response->type('application/json'); 
      ini_set('max_execution_time', 1800); //1800 seconds = 30 minutes     
      $start = microtime(true);    
      $response = array("type"=>"error", "message"=>"No work got done!");
                  

      if ($this->request->is('post')) {

          $project = $this->Projects->newEntity();
          $project = $this->Projects->patchEntity($project, $this->request->data);
          $handshake = $project->handshake_code;


          if(!$handshake){ // If there is no handshake code received, start new procedure

              $project->archive_folder_path = "file:///".$project->root_folder_path."/";           
              $fixedFolderName = strtolower(str_replace(' ', '_' , $project->name));

              if(!is_dir($project->archive_folder_path)) {
                $response["message"] = "Not a valid folder path."; 
                $response["duration"] = microtime(true) - $start;
                echo json_encode($response);
                die();    
              }

              if($project->ignore_tmp && !is_dir($project->tmp_folder_path)) {
                $response["message"] = "Not a valid temporary folder path."; 
                $response["duration"] = microtime(true) - $start;
                echo json_encode($response);
                die();    
              }

              if(!$project->ignore_tmp){
                $project->tmp_folder_path = "";
              }

              if ($this->Projects->save($project)) {

                $procedure = $this->Procedures->newEntity();
                $procedure->project_id = $project->id;
                $procedure->handshake_code = $this->incrementalHash();
                $procedure->current_procedure = "First";
                $procedure->stepsToGo = 3; // Three mandatory steps
                if($project->copy_first_version_to_repository) { $procedure->stepsToGo = $procedure->stepsToGo + 1;}
                if($project->mark_first_push) { $procedure->stepsToGo = $procedure->stepsToGo + 1;}

                if($this->Procedures->save($procedure)){

                    $result = $this->createArchiveStructure($project, $procedure);

                    $response["message"] = $result["message"];
                    if($result["type"] == "error"){
                      $response["duration"] = microtime(true) - $start;
                      echo json_encode($response);
                      die();    
                    }

                    
                    $response["stepsCompleted"] = $result["message"];
                    $response["nextTask"] = $result["nextTask"];
                    $procedure->due_procedure = "saveFilesToRepository";
                    $procedure->stepsCompleted = $procedure->stepsCompleted+1;
                    $procedure->stepsToGo = $procedure->stepsToGo-1;
                    
                    if($this->Procedures->save($procedure)){
                      $response["type"] = $result["type"];
                      $response["procedure"] = $procedure;
                      $response["handshake_code"] = $procedure->handshake_code;
                    }
                    else {
                      $response["message"] = "Procedure could not be updated."; 
                      $response["duration"] = microtime(true) - $start;
                      echo json_encode($response);
                      die();                    
                    }

                } else {
                  $response["message"] = "Procedure could not be created."; 
                  $response["duration"] = microtime(true) - $start;
                  echo json_encode($response);
                  die();
                }                

              } else {
                $response["message"] = "Project could not be created.";     
                $response["project"] = $this->request->data;      
                $response["duration"] = microtime(true) - $start;
                echo json_encode($response);
                die();
              }

          } else { // There is an handshake code received, validate it and continue procedure

              // Get Procedure and Project
              $procedure = $this->Procedures->find('all', [
                  'conditions' => ['handshake_code' => $handshake]
              ])->first();
              $project = $this->Projects->findById($procedure->project_id)->first();

              $procedureMethod = $procedure->due_procedure; // Get procedure to call
              $result = $this->$procedureMethod($project, $procedure); // Call the next method in line

              $response["message"] = $result["message"];
              $procedure->stepsCompleted = $procedure->stepsCompleted+1;
              $procedure->stepsToGo = $procedure->stepsToGo-1;
              $response["nextTask"] = $result["nextTask"];
              $procedure = $result["procedure"];

              if($this->Procedures->save($procedure)){
                $response["type"] = $result["type"];
                $response["procedure"] = $procedure;
                $response["project"] = $project;
                $response["handshake_code"] = $procedure->handshake_code;
                $response["fileAvailable"] = $result["fileAvailable"];
              }
              else {
                $response["message"] = "Procedure could not be updated."; 
                $response["duration"] = microtime(true) - $start;
                echo json_encode($response);
                die();                    
              }
          }

      }
      $response["duration"] = microtime(true) - $start;

      echo json_encode($response);
    }


    /*
    * Process
    * Starts and continues a new project creation process
    * based on an handshake string received. If handshake is null
    * a new process is initiated
    * @returns json with operation info
    */
    public function download($procedureHandshake) {

      $this->loadModel('Procedures');
      $this->loadModel('Projects');
      $procedure = $this->Procedures->find('all', [
          'conditions' => ['handshake_code' => $procedureHandshake]
      ])->first();
    
      $project = $this->Projects->get($procedure->project_id);
      $projectName = strtolower(str_replace(' ', '_' , $project->name));

      $path = WWW_ROOT.DS.'project_sources'.DS.$projectName.DS.'compressed.zip';

      $this->response->file($path, array(
          'download' => true,
          'name' => $project->name.'file.zip',
      ));

      return $this->response;

    }



    /*
    * Process
    * Starts and continues a new project creation process
    * based on an handshake string received. If handshake is null
    * a new process is initiated
    * @returns json with operation info
    */
    public function downloadFiles($id) {

      $this->loadModel('Projects');

    
      $project = $this->Projects->get($id);
      $projectName = strtolower(str_replace(' ', '_' , $project->name));

      $path = WWW_ROOT.DS.'project_sources'.DS.$projectName.DS.'compressedfiles.zip';

      $this->response->file($path, array(
          'download' => true,
          'name' => $project->name.'file.zip',
      ));

      return $this->response;

    }


    /*
    * Download All
    * Downloads all commited files that are not pushed
    * @param id: Project ID
    * @returns json with operation info
    */
    public function downloadAllAjax($id = null)
    {

        //Prepare ajax data
        ini_set('max_execution_time', 300); //300 seconds = 5 minutes
        $start = microtime(true);
        $response = array("type"=>"error", "message"=>"Error downloading files.");
        $this->autoRender = false;
        $this->RequestHandler->respondAs('json');
        $this->response->type('application/json');  
        $this->loadModel('Projects');
        $this->loadModel('Files');

        //Get Project
        $project = $this->Projects->get($id);

        // Get current files
        $fileRecords = $this->Files->find('all')->where(["Files.project_id" => $project->id, "Files.pushed" => 0]);
        
        if(!count($fileRecords)) {

            $response["message"] = 'No files to be downloaded.';
           echo json_encode($response);
            die();

        }

        $projectName = strtolower(str_replace(' ', '_' , $project->name));

        $zipped = $this->Projects->create_zip_files($fileRecords, WWW_ROOT.DS.'project_sources'.DS.$projectName.DS.'compressedfiles.zip', $project);


        if($zipped["status"] == "success"){

            $response["message"] = $zipped["message"];
            $response["type"] = "success";
            $response["projectid"] = $project->id;


        } else {

            $response["message"] = 'Could not download files.';
            $response["detailedError"] = $zipped["message"];
            echo json_encode($response);
            die();

        }
        echo json_encode($response);
    }



    /********************************************************
    ******************PRIVATE METHODS************************
    ********************************************************/


    /***PROJECT CREATION METHODS***/




    /*
    * Save Files to Repository
    * Registers set of files in files table
    * @param project: The project entity
    * @param procedure: The current procedure entity
    * @returns array with operation info
    */
    private function saveFilesToRepository($project, $procedure){

      $this->loadModel('Files');
      $this->loadModel('Projects');
      $response = array("type"=>"error", "message"=>"Task failed!", "fileAvailable" => false);
      $interruptProcess = false;

      // Add Files to Files Table
      $files = array();
      $files = $this->Projects->getFolderFiles($project->archive_folder_path,$files);
      $filerecords = array();
      $fixedFolderName = strtolower(str_replace(' ', '_' , $project->name));
      $structure = './project_sources/'.$fixedFolderName;
      $excludeTemp = $project->tmp_folder_path;

      foreach($files as $file){

          if(!$interruptProcess) {

              $filerecord = $this->Files->newEntity();
              $filerecord->project_id = $project->id;
              $filerecord->path = $file['path'];
              $filerecord->name = $file['name'];
              $filerecord->size = $file['size'];
              $filerecord->extension = $file['extension'];
              $filerecord->type = $file['type'];
              $filerecord->last_modified = $file['filemodified'];
              $filerecord->system_file_created_on = $file['filecreated']; // To deal with files with dots in name, which causes duplicates on table
              $filerecord->commited = 1; // By the time the file is saved, it would have been copied to repository


              //Test if temporary folder not to be copied
              if($excludeTemp && $this->isInPath($file['path'], $excludeTemp) ) {
                continue;
              }


              //Copy file to repository
              $pathWithoutRoot = str_replace('file:///'.$project->root_folder_path, "", $file['path']);
              $newPath = str_replace('file:///'.$project->root_folder_path, $structure, $file['path']);
              $completeFileName = $file['name'].".".$file['extension'];

              // Test if directory system exists and create in case it doesn't
              if(!is_dir($newPath)){
                if(!mkdir($newPath, 0777, true)){
                  $response["message"] = "Could not create directories.";
                  $interruptProcess = true;
                }
              }

              if(!copy($file['path']."\\".$completeFileName, $newPath."\\".$completeFileName)){
                $response["message"] = "File could not be copied.";
                $interruptProcess = true;
              }

              array_push($filerecords, $filerecord);

          } 
      }

      if(!$interruptProcess){
          if ($this->Files->saveMany($filerecords)) {
              $response["type"] = "success";
              $response["message"] = "Files saved to repository.";
          } else {
              $response["message"] = "Files could not be saved to repository database.";
          }       
      }

      $procedure->due_procedure = "createVersion";
      return array("fileAvailable" => $response["fileAvailable"], "nextTask" => "Preparing version...", "message" => $response["message"], "type" => $response["type"], "procedure" => $procedure);
    }




    /*
    * Create Version
    * Registers first version of the project
    * @param project: The project entity
    * @param procedure: The current procedure entity
    * @returns array with operation info
    */
    private function createVersion($project, $procedure){

      $this->loadModel('Versions');
      $this->loadModel('Projects');
      $response = array("type"=>"error", "message"=>"Task failed!", "fileAvailable" => false);

      // Create first Version
      $version = $this->Versions->newEntity(); 
      $version->project_id =  $project->id;
      $version->version_number =  "1";

      if ($this->Versions->save($version)) {

          $response["type"] = "success";
          $response["message"] = "Version created.";

      } else {
          $response["message"] = "The Version could not be saved.";
      }

      $procedure->due_procedure = "";
      $nextTask = "Finalizing...";
      if($project->copy_first_version_to_repository) {
        if($project->transfer_type == "zip_download") {
          $procedure->due_procedure = "zipFiles";
          $nextTask = "Zipping Files...";
        } 
        if($project->transfer_type == "ftp_transfer") {
          $procedure->due_procedure = "sendFilesFTP";
          $nextTask = "Sending Files...";
        } 
      }      
      return array("fileAvailable" => $response["fileAvailable"], "nextTask" => $nextTask, "message" => $response["message"], "type" => $response["type"], "procedure" => $procedure);
    }




    /*
    * Zip Files
    * Compresses project files into a zip file in project source for download
    * @param project: The project entity
    * @param procedure: The current procedure entity
    * @returns array with operation info
    */
    private function zipFiles($project, $procedure) {

      $this->loadModel('Projects');
      $response = array("type"=>"error", "message"=>"Archive compression failed!", "fileAvailable" => false);
      
      //ZIP FILES          

      $zipped = $this->Projects->create_zip(WWW_ROOT.DS.'project_sources'.DS.$project->name, WWW_ROOT.DS.'project_sources'.DS.$project->name.DS.'compressed.zip', $project);

      $nextTask = "Finalizing...";
      if($zipped["status"] == "success") {
        $response["type"] = "success";
      }
      $response["message"] = $zipped["message"];
      
      $fileAvailable = true;
      $procedure->due_procedure = "";
      if($project->mark_first_push) { 
        $nextTask = "Marking files as pushed...";
        $procedure->due_procedure = "markPushed"; 
      }
      
      return array("fileAvailable" => $fileAvailable, "nextTask" => $nextTask, "message" => $response["message"], "type" => $response["type"], "procedure" => $procedure);
    }




    /*
    * Send FTP
    * Sends project files to remote server location through FTP
    * @param project: The project entity
    * @param procedure: The current procedure entity
    * @returns array with operation info
    */
    private function sendFilesFTP($project, $procedure) {

      $this->loadModel('Projects');
      $response = array("type"=>"error", "message"=>"Task failed!", "fileAvailable" => false);

      require_once(ROOT .DS. "Vendor" . DS  . "versioncontrol" . DS . "ftpal" . DS . "ftpal.php");    
      $ftp_server = $project->ftp_server; 
      $ftp_user_name = $project->ftp_user_name; 
      $ftp_user_pass = $project->ftp_password; 

      $ftpCon = new FTPAL($ftp_server, $ftp_user_name, $ftp_user_pass);
      $ftpCon->connect();

      
      // Add Files to Files Table
      $files = array();
 
      $files = $this->Projects->getFolderFiles($project->archive_folder_path,$files);
      $filerecords = array();

      foreach($files as $file){

          $completePath = $file['path'];
          $relativePath = str_replace('file:///'.$project->root_folder_path, "", $completePath);
          $relativePath = str_replace("\\", "/", $relativePath);
          if($relativePath!=""){
              //echo "Trying to create ".$relativePath."<br>"; 

              if($ftpCon->createFolderStructureIfNotExists($relativePath)) {

                  //echo "Trying to copy ".$relativePath."/".$file['name'].".".$file['extension']."<br>"; 
                  if(!$resultUP = $ftpCon->uploadFile($completePath."\\".$file['name'].".".$file['extension'], $relativePath."/".$file['name'].".".$file['extension'])) {
                      $response["message"] = 'File could not be copied.';
                      $ftpCon->close(); 
                      echo json_encode($response);
                      die();                    
                  }

              } else {                    
                  $response["message"] = 'Structure could not be created.';
                  $ftpCon->close(); 
                  echo json_encode($response);
                  die();  
              }

          } else{
              //echo "Trying to copy ".$relativePath."/".$file['name'].".".$file['extension']."<br>"; 
              if(!$resultUP = $ftpCon->uploadFile($completePath."\\".$file['name'].".".$file['extension'], $relativePath."/".$file['name'].".".$file['extension'])) {
                  $response["message"] = 'File could not be copied.';
                  $ftpCon->close(); 
                  echo json_encode($response);
                  die();                    
              }
          }

      }

      $ftpCon->close();

      $response["message"] = "Files were sent";
      $procedure->due_procedure = "markPushed"; 
      $response["type"] = "success";
      return array("fileAvailable" => $response["fileAvailable"], "nextTask" => "Finalizing...", "message" => $response["message"], "type" => $response["type"], "procedure" => $procedure);
    }




    /*
    * Mark Pushed 
    * Marks all project files as pushed
    * @param project: The project entity
    * @param procedure: The current procedure entity
    * @returns array with operation info
    */
    private function markPushed($project, $procedure){

      $response = array("type"=>"error", "message"=>"Task failed!", "fileAvailable" => false);
      $this->loadModel('Files');
      $this->loadModel('Projects');

      $filesPushed = $this->Files->updateAll(['pushed' => '1'], ['project_id' => $project->id, 'pushed' => '0']);

      if($filesPushed) {
        $response["message"] = "Files were marked as pushed";
        $response["type"] = "success";      
      }

      $procedure->due_procedure = "";

      return array("fileAvailable" => false, "nextTask" => "Finalizing...", "message" => $response["message"], "type" => $response["type"], "procedure" => $procedure);
    }



    /*
    * Create Archive Structure 
    * Creates archive folder structure in project
    * @param project: The project entity
    * @param procedure: The current procedure entity
    * @returns array with operation info
    */
    private function createArchiveStructure($project, $procedure){

        $response = array("type"=>"error", "message"=>"Task failed!", "fileAvailable" => false);
        $this->loadModel('Projects');

        $project->archive_folder_path = "file:///".$project->root_folder_path."/";
        $fixedFolderName = strtolower(str_replace(' ', '_' , $project->name));
        $archiveStructure = './project_sources/'.$fixedFolderName."/archive";

        // Create Repository
        if(!is_dir($archiveStructure)){
          if(mkdir($archiveStructure, 0777, true)){
            $response["type"] = "success";
            $response["message"] = "Archive structure created.";
          }
        }
        else {
          $response["message"] = "Project file system already exists. Please delete and create project.";
        }

        return array("nextTask" => "Saving files to repository...", "message" => $response["message"], "type" => $response["type"]);
    }

    /***COMMIT AND PUSH METHODS***/


    /*
    * Get Changes
    * Searches database for changes made to project comparing files table with tempfiles table
    * @param projectId: the ID of the project to search for changes
    * @returns Query result array: Changes made to project (Additions, Modifications and additions)
    */
    private function getChanges($projectId) {

        $conn = ConnectionManager::get('default');

        $stmt = $conn->execute("SELECT * FROM
                        (
                            SELECT
                            `fileId`,
                            `fileName`,
                            `fileExtension`,
                            `filePath`,
                            `fileType`,
                            `fileLastModified`,
                            `fileOrigin`,
                            `fileSize`,
                            `fileDeleted`,
                            `TopVal`,
                            `MinVal`,
                            CASE
                            WHEN
                            Quantity=1 AND
                            fileOrigin='database'
                            THEN
                            'deleted'
                            WHEN
                            Quantity=1 AND
                            fileOrigin='real_path'
                            THEN
                            'added'
                            WHEN
                            Quantity=2 AND
                            TopVal <> MinVal
                            THEN
                            'modified'
                            ELSE
                            ''
                            END AS changeType

                            FROM  
                            (
                                SELECT * FROM
                                (
                                  SELECT *, 
                                  (SELECT MAX(fileLastModified) FROM v WHERE v.fileName = base.fileName AND v.filePath = base.filePath AND v.fileExtension = base.fileExtension LIMIT 1) as TopVal,
                                  (SELECT MIN(fileLastModified) FROM v WHERE v.fileName = base.fileName AND v.filePath = base.filePath AND v.fileExtension = base.fileExtension LIMIT 1) as MinVal
                                   FROM
                                  (
                                    SELECT
                                    `fileId`,
                                    `fileName`,
                                    `fileExtension`,
                                    `filePath`,
                                    `fileType`,
                                    `fileLastModified`,
                                    `fileSize`,
                                    `fileOrigin`,
                                    `fileDeleted`,
                                    (SELECT COUNT(*) FROM v as temp WHERE temp.fileName=v.fileName AND temp.filePath=v.filePath  AND temp.fileExtension=v.fileExtension AND temp.fileExtension = v.fileExtension LIMIT 2) as Quantity
                                    FROM v
                                        WHERE v.projectId = ".$projectId." AND v.fileDeleted = 0
                                    ) as base
                                ) as grouping
                                GROUP BY fileName, filePath
                            ) as calculations  
                        )
                        as changeTypes
                        WHERE changeType <> ''
                        ORDER BY filePath ASC");

        return $stmt->fetchAll('assoc');

    }


    /*
    * Get Current Files
    * Searches for all files present in project's root folder and subfolders
    * @param projectId: the ID of the project to search for files
    * @param projectArchiveFolderPath: the project's root folder path
    * @returns Array: Files in project folder
    */
    private function getCurrentFiles($projectId, $projectArchiveFolderPath) {

        $files = array();
        $files = $this->Projects->getFolderFiles($projectArchiveFolderPath,$files);
        $fileRecords = array();
        $this->loadModel('TempFiles');

        foreach($files as $file){

            $filerecord = $this->Tempfiles->newEntity();
            $filerecord->project_id = $projectId;
            $filerecord->path = $file['path'];
            $filerecord->name = $file['name'];
            $filerecord->size = $file['size'];
            $filerecord->extension = $file['extension'];
            $filerecord->type = $file['type'];
            $filerecord->last_modified = $file['filemodified'];
            $filerecord->system_file_created_on = $file['filecreated']; // To deal with files with dots in name, which causes duplicates on table
            $filerecord->commited = 1; // By the time the files are saved, they will have been copied to the repository

            array_push($fileRecords, $filerecord);
        }
        return $fileRecords;
    }


    /*
    * Format Files
    * Formats files in specific array configuration
    * @param files: files to be formatted
    * @returns Array: Formatted files
    */
    private function formatFiles($files){

        $formatedFilesArray = array();

        foreach($files as $file) {
            array_push($formatedFilesArray, array("id" => $file->id, "name" => $file->name, "path" => $file->path, "size" => $file->size, "type" => $file->type, "extension" => $file->extension, "fileModified" => $file->last_modified, "fileCreated" => $file->system_file_created_on));
        }
        
        return $formatedFilesArray;

    }


    /*
    * Mark Pushed
    * Marks a single file as pushed or not pushed
    * @param fileId: ID of file to mark as pushed
    * @param isPushed: true will mark file as pushed, false will mark it as not pushed
    * @returns true if file change successful, false otherwise
    */
    private function markPushedOrNot ($fileId, $isPushed) {

        $this->loadModel('Files');

        $file = $this->Files->get($fileId);

        $file->pushed = $isPushed;

        return $this->Files->save($file);

    }



    /***FTP METHODS***/

    /*
    * Delete FTP
    * Deletes a set of files from remote FTP location
    * @param project: Project instance
    * @param files: Files to be deleted
    * @param server: FTP Server address
    * @param username: FTP Server username
    * @param password: FTP Server password
    * @returns result: information of deletion process
    */
   private function deleteFTP($project, $files, $server, $username, $password) {

      require_once(ROOT .DS. "Vendor" . DS  . "versioncontrol" . DS . "ftpal" . DS . "ftpal.php");    

      $ftp_server = $server; 
      $ftp_user_name = $username; 
      $ftp_user_pass = $password; 
      $files = $this->formatFiles($files);
      $result = array("filesToDelete" => count($files), "filesDeleted" => 0);


      $ftpCon = new FTPAL($ftp_server, $ftp_user_name, $ftp_user_pass);
      $ftpCon->connect();

      foreach($files as $file){

          $completePath = $file['path'];
          $relativePath = str_replace('file:///'.$project->root_folder_path, "", $completePath);
          $relativePath = str_replace("\\", "/", $relativePath);


          if(!$resultUP = $ftpCon->deleteFile($relativePath."/".$file['name'].".".$file['extension'])) {
              $ftpCon->close(); 
              return $result;                    
          }
          $result["filesDeleted"]++;
          
      }

      $ftpCon->close();
      return $result;
    }



    /*
    * Send FTP
    * sends a set of files to remote FTP location
    * @param project: Project instance
    * @param files: Files to be sent
    * @param server: FTP Server address
    * @param username: FTP Server username
    * @param password: FTP Server password
    * @returns result: information of sending process
    */
    private function sendFTP($project, $files, $server, $username, $password) {

      require_once(ROOT .DS. "Vendor" . DS  . "versioncontrol" . DS . "ftpal" . DS . "ftpal.php");    
      $ftp_server = $server; 
      $ftp_user_name = $username; 
      $ftp_user_pass = $password; 
      $files = $this->formatFiles($files);
      $result = array("filesToSend" => count($files), "filesSent" => 0);


      $ftpCon = new FTPAL($ftp_server, $ftp_user_name, $ftp_user_pass);
      $ftpCon->connect();

      foreach($files as $file){

          $completePath = $file['path'];
          $relativePath = str_replace('file:///'.$project->root_folder_path, "", $completePath);
          $relativePath = str_replace("\\", "/", $relativePath);
          if($relativePath!=""){

              if($ftpCon->createFolderStructureIfNotExists($relativePath)) {

                    if(!$resultUP = $ftpCon->uploadFile($completePath."\\".$file['name'].".".$file['extension'], $relativePath."/".$file['name'].".".$file['extension'])) {
                      $response["message"] = 'File could not be copied.';
                      $ftpCon->close(); 
                      return $result;                   
                    }
                    $result["filesSent"]++;
                    $this->markPushedOrNot($file["id"], 1);
              } else {                  
                  $ftpCon->close(); 
                  return $result;  
              }

          } else{

              if(!$resultUP = $ftpCon->uploadFile($completePath."\\".$file['name'].".".$file['extension'], $relativePath."/".$file['name'].".".$file['extension'])) {
                  $ftpCon->close(); 
                  return $result;                    
              }
              $result["filesSent"]++;
              $this->markPushedOrNot($file["id"], 1);
          }

      }

      $ftpCon->close();
      return $result;
    }



    /**AUXILIARY METHODS*/

    /*
    * Incremental Hash
    * Returns a random string with a given length
    * @param (optional) len: length of the return string
    * @returns string
    */
    private function incrementalHash($len = 15){

        $charset = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
        $base = strlen($charset);
        $random_string = '';

        for ($i = 0; $i < $len; $i++){
          $random_pick = mt_rand(1, $base);
          $random_char = $charset[$random_pick-1];
          $random_string .= $random_char;
        }

        return $random_string;

    }


    /*
    * Is In Path
    * Checks whether a path exists whithin another path
    * @param filePath: File path to look up
    * @param matchPath: Path to match in file path
    * @returns false if match path doesn't have a match in file path, position number otherwise
    */
    private function isInPath($filePath, $matchPath) {

      $filePath = str_replace("\\", "/", $filePath);
      $matchPath = str_replace("\\", "/", $matchPath);

      return strpos($filePath, $matchPath);
    
    }


}