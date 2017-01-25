<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Datasource\ConnectionManager;
use Cake\Utility\Text;
use ZipArchive;
use ftpal\ftpal;

/**
 * Projects Controller
 *
 * @property \App\Model\Table\ProjectsTable $Projects
 */
class ProjectsController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index()
    {

        $projects = $this->paginate($this->Projects->find('all', ['conditions' => ['Projects.deleted' => 0]])->contain(['Versions']));
        $this->set(compact('projects'));
        $this->set('_serialize', ['projects']);

    }

    /**
     * Edit method
     *
     * @param string|null $id Project id.
     * @return \Cake\Network\Response|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $project = $this->Projects->get($id, [
            'contain' => ["Versions"]
        ]);


        if ($this->request->is(['patch', 'post', 'put'])) {

            $ignoreTemp = $this->request->data("ignore_tmp");
            
            $tmp_folder_path = $this->request->data("tmp_folder_path");

            if($ignoreTemp && !is_dir($tmp_folder_path)) {

                $this->Flash->error(__('Not a valid folder path: '. $tmp_folder_path));

            } else {

                $project->ignore_tmp = $ignoreTemp;

                if($project->ignore_tmp) { 
                  
                    $project->tmp_folder_path = $tmp_folder_path; 


                  } else { 

                    $project->tmp_folder_path = ""; 

                  }

                if ($this->Projects->save($project)) {

                    $this->Flash->success(__('The project has been saved.'));

                    return $this->redirect(['action' => 'index']);
                
                } else {

                    if($project->errors()){
                        $error_msg = [];
                        foreach( $project->errors() as $errors){
                            if(is_array($errors)){
                                foreach($errors as $error){
                                    $error_msg[]    =   $error;
                                }
                            }else{
                                $error_msg[]    =   $errors;
                            }
                        }

                        if(!empty($error_msg)){
                            $this->Flash->error(
                                __("Please fix the following error(s):".implode("\n \r", $error_msg))
                            );
                        }
                    }

                    $this->Flash->error(__('The project could not be saved. Please, try again.'));
                }

            }

        }

        //$currentVersions = $this->Projects->CurrentVersions->find('list', ['limit' => 200]);
        $this->set(compact('project'));
        $this->set('_serialize', ['project']);
    }

    /**
     * View method
     *
     * @param string|null $id Project id.
     * @return \Cake\Network\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {

        if ($this->request->is(['patch', 'post', 'put'])) {
          $this->loadModel('Files');

          $files = array();
          $files = $this->Projects->getFolderFiles($project->archive_folder_path,$files);
          $filerecords = array();
          foreach($files as $file){
              //$filerecord = $this->Files->newEntity();
              $filerecord->project_id = $project->id;
              $filerecord->name = $file['name'];
              $filerecord->name = $file['path'];
              $filerecord->size = $file['size'];
              $filerecord->extension = $file['extension'];
              $filerecord->type = $file['type'];
              $filerecord->last_modified = $file['filemodified'];

              array_push($filerecords, $filerecord);
          }
        }

        /*$project = $this->Projects->get($id, [
            'contain' => ['CurrentVersions', 'Files', 'Versions']
        ]);*/
         $project = $this->Projects->get($id, [
            'contain' => ['Files', 'Versions']
        ]);       

        $this->set('project', $project);
        $this->set('_serialize', ['project']);
    }



    /**
     * Add method
     *
     * @return \Cake\Network\Response|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        ini_set('max_execution_time', 300); //300 seconds = 5 minutes
        $project = $this->Projects->newEntity();
        if ($this->request->is('post')) {
            $project = $this->Projects->patchEntity($project, $this->request->data);
            $project->archive_folder_path = "file:///".$project->root_folder_path."/";

            $this->loadModel('Versions');

            $fixedFolderName = strtolower(str_replace(' ', '_' , $project->name));
            $structure = './project_sources/'.$fixedFolderName;
            $archiveStructure = './project_sources/'.$fixedFolderName."/archive";

            // Create Repository
            if(!is_dir($archiveStructure)){
              mkdir($archiveStructure, 0777, true);
            }
            else {
              die("Project file system already exists. Please delete and create project.");
            }

            if ($this->Projects->save($project)) {

                $this->loadModel('Versions');
                $this->loadModel('Files');

                // Add Files to Files Table
                $files = array();
                $files = $this->Projects->getFolderFiles($project->archive_folder_path,$files);
                $filerecords = array();

                foreach($files as $file){
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

                    
                    //Copy file to repository
                    $pathWithoutRoot = str_replace('file:///'.$project->root_folder_path, "", $file['path']);
                    $newPath = str_replace('file:///'.$project->root_folder_path, $structure, $file['path']);
                    $completeFileName = $file['name'].".".$file['extension'];

                    // Test if directory system exists and create in case it doesn't
                    if(!is_dir($newPath)){
                      if(!mkdir($newPath, 0777, true)){
                        print_r('Could not create directories.');
                        die();
                      }
                    }

                    if(!copy($file['path']."\\".$completeFileName, $newPath."\\".$completeFileName)){
                      print_r("Create : ".$newPath.$completeFileName);
                      print_r("<br/>");
                      die("File could not be copied.");
                    }

                    array_push($filerecords, $filerecord);
                }

                if ($this->Files->saveMany($filerecords)) {

                    // Create first Version
                    $version = $this->Versions->newEntity(); 
                    $version->project_id =  $project->id;
                    $version->version_number =  "1";

                    if ($this->Versions->save($version)) {

                        $this->Flash->success(__("The Project's first version has been saved."));

                        return $this->redirect(['action' => 'index']);

                    } else {
                        $this->Flash->error(__('The version could not be saved.'));
                    }

                    return $this->redirect(['action' => 'index']);

                } else {
                    $this->Flash->error(__('Files could not be saved.'));
                }

            }

        }
        //$currentVersions = $this->Projects->CurrentVersions->find('list', ['limit' => 200]);
        $this->set(compact('project'));
        $this->set('_serialize', ['project']);
    }



    /**
     * Delete method
     *
     * @param string|null $id Project id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $project = $this->Projects->get($id);
        $project->deleted = 1;
        $project->name = $project->name;

        // Change Project Folder Name
        //$project->archive_folder_path = "file:///".$project->root_folder_path."/";
        $fixedFolderName = strtolower(str_replace(' ', '_' , $project->name));
        $folderStructure = './project_sources/'.$fixedFolderName;

        $newFolderDeletedName = $folderStructure."_deleted";

        while(is_dir($newFolderDeletedName)) {
          $newFolderDeletedName.="_";
        }

        $renamed=@rename($folderStructure, $newFolderDeletedName);              
        

        if ($renamed && $this->Projects->save($project)) {
            $this->Flash->success(__('The project has been deleted.'));
        } else {
            $this->Flash->error(__('The project could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }



}
