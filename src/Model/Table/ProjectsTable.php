<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Utility\Text;
use ZipArchive;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * Projects Model
 *
 * @property \Cake\ORM\Association\HasMany $Files
 * @property \Cake\ORM\Association\HasMany $Versions
 *
 * @method \App\Model\Entity\Project get($primaryKey, $options = [])
 * @method \App\Model\Entity\Project newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Project[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Project|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Project patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Project[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Project findOrCreate($search, callable $callback = null)
 */
class ProjectsTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->table('projects');
        $this->displayField('id');
        $this->primaryKey('id');

        /*$this->belongsTo('CurrentVersions', [
            'foreignKey' => 'current_version_id',
            'joinType' => 'INNER'
        ]);*/
        $this->hasMany('Files', [
            'foreignKey' => 'project_id'
        ]);
        $this->hasMany('Versions', [
            'foreignKey' => 'project_id'
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmpty('id', 'create');

        $validator
            ->requirePresence('name', 'create')
            ->notEmpty('name');

        $validator
            ->requirePresence('root_folder_path', 'create')
            ->notEmpty('root_folder_path');

        $validator
            ->dateTime('last_change');

        $validator
            ->dateTime('created_on');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        //$rules->add($rules->existsIn(['current_version_id'], 'CurrentVersions'));

        return $rules;
    }

    public static function readfiles($path){
        $filecount = 0;
        $filelist = "";
        if (is_dir($path)) {
            if ($dh = opendir($path)) {
                while (($file = readdir($dh)) !== false) {
                    $filelist = $filelist . "filename: $file : filetype: " . filetype($path . $file). " - filesize: " . filesize($path . $file) . ";";
                    $filecount++;
                }
                closedir($dh);
            }
        }  
        return $filecount;

    }

    public function listFolderFiles($dir){
        $ffs = scandir($dir);
        $files = '<ol>';
        foreach($ffs as $ff){
            if($ff != '.' && $ff != '..'){
                $files = $files . '<li>'.$ff;
                if(is_dir($dir.'/'.$ff)) $files = $files . $this->listFolderFiles($dir.'/'.$ff);
                $files = $files . '</li>';
            }
        }
        return $files . '</ol>';
    }


    public function getFolderFiles($dir, $resultArray){
        $rootpath = $dir;
        $fileinfos = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($rootpath, RecursiveDirectoryIterator::SKIP_DOTS)
        );
        foreach($fileinfos as $fileinfo) {
            $pathName = $fileinfo->getPathname();
            $pathDivision = explode("\\",$pathName); //first char of filename
            $fileString = utf8_encode(end($pathDivision));
            

            if(!strrpos($fileString, '.') || $fileString[0]=='.') { // If first char is dot, or there are no dots
                $fileName = $fileString;
                $fileExtension = "";
            }
            else {
                $fileNameDivision = explode('.', $fileString);
                $fileExtension = array_pop($fileNameDivision);
                $fileName = implode('.',$fileNameDivision);
            }
            array_push($resultArray, array("name" => $fileName, "path" => $fileinfo->getPath() , "size" => $fileinfo->getSize(), "type" => $fileinfo->getType(), "extension" => $fileExtension, "filemodified" => $fileinfo->getMTime(), "filecreated" => $fileinfo->getCTime()));
        }

        return $resultArray;
    }

    public function uploadFiles($file, $folderName){

        $filename = $file['name'];
        $file_tmp_name = $file['tmp_name'];
        $dir = WWW_ROOT.'project_sources'.DS.$folderName;
        $unique = Text::uuid();
        $fileNewName = $unique.'-'.$filename;
        move_uploaded_file($file_tmp_name, $dir.DS.$fileNewName);

        $zip = new ZipArchive;
        $res = $zip->open($dir.DS.$fileNewName);
        if ($res === TRUE) {
          $zip->extractTo($dir);
          $newfolderfiles = scandir($dir);
          $newFolderName = "";
          foreach($newfolderfiles as $singleFile){
            if(is_dir($dir.DS.$singleFile)){
                if($singleFile != '.' && $singleFile != '..' && $singleFile != 'archive_folder'){
                    $newFolderName = $singleFile;
                }
            }
                
          }

          $zip->close();

        } else {
          die('doh');
        }

        return $newFolderName;
    }

    public function renameFolder($path){

          $dir = WWW_ROOT.$path;
          $renamed = false;
          $i = 0;

          while(!$renamed && $i<5) { // Try to rename for five times
            try {
              $renamed=@rename($dir.DS.$newFolderName, $dir.DS.'source');              
            } catch (Exception $e) {
                $i++;
            }
          }  
          return $renamed;
    }


    public function create_zip($source, $destination, $project) {

        $result = array("message" => "Could not start zip archive", "status" => "error");

        $emptyTempDir = false;        

        $source = strtolower(str_replace(' ', '_' , $source));
        $source = strtolower(str_replace('\\\\', '\\' , $source));


        $destination = strtolower(str_replace(' ', '_' , $destination));
        $destination = strtolower(str_replace('\\\\', '\\' , $destination));

        if (!extension_loaded('zip') || !file_exists($source)) {
            $result["message"] = "No zip extension or no such file source.";
            if(!file_exists($source)){
                $result["message"] = "No such file source: ".$source;

            }
            return $result;
        }

        $zip = new ZipArchive();

        if (!$zip->open($destination, ZIPARCHIVE::CREATE)) {
            $result["message"] = "Could not open destination.";
            return $result;
        }



        //$source = str_replace('\\', '/', $source);
        $testPaths = "";
        if (is_dir($source) === true)
        {
            $fileinfos = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS)
            );
            
            $addedFiles = array();
            foreach ($fileinfos as $fileInfo)
            {

                $file = str_replace('\\', '/', $fileInfo);

                // Ignore "." and ".." folders
                if( in_array(substr($file, strrpos($file, '/')+1), array('.', '..')) )
                    continue;

                //$filePath = realpath($file);

                if (is_dir($file) === true)
                {
                    $zip->addEmptyDir(str_replace($source . '/', '', $file . '/'));
                }
                else if (is_file($file) === true)
                {

                    $fullPath = str_replace('\\', '/', $fileInfo->getPathname());

                    $newPath = substr($fullPath, strpos($fullPath, "project_sources") + 15); // Remove folder structure until project root

                    // Get tmp path relative to project folder
                    $relativeTmp = $project->name."/".str_replace($project->root_folder_path, "", $project->tmp_folder_path); // Get tmp path in format projectName/path/to/tmp

                    $newPathFolder = explode("\\", $newPath);
                    if(count($newPathFolder)>1) { array_pop($newPathFolder); }
                    $newPathFolder = implode("\\", $newPathFolder);

                    // If ignore temp files checked test if it is a file from the temporary folder
                    if(!$project->ignore_tmp || !$this->pathExistsIn($newPathFolder, $relativeTmp)) { 

                        $added = $zip->addFile($fullPath, $newPathFolder);
                        
                    }  
                    else if(!$emptyTempDir) { // Create temporary empty folder                            
                        $zip->addEmptyDir($relativeTmp);
                        $emptyTempDir = true;                        
                    }                      
 
                }
            }
        }
        else if (is_file($source) === true)
        {
            $zip->addFromString(basename($source), file_get_contents($source));
        }


        if($zip->close()) {
            $result["message"] = "Zip process complete.";
            $result["status"] = "success";
        } else {
            $result["message"] = "Could not close component.";
        }

        return $result;
    }


    public function mock(){
        $result = array("message" => "Mock successful", "status" => "success");
        return $result;
    }

    public function create_zip_files($files, $destination, $project) {
        


        $result = array("message" => "Could not start zip archive", "status" => "error");

        $emptyTempDir = false;        
        @unlink($destination); // Delete file if already exists
        $destination = strtolower(str_replace(' ', '_' , $destination));
        $destination = strtolower(str_replace('\\\\', '\\' , $destination));

        if (!extension_loaded('zip')) {
            $result["message"] = "No zip extension or no such file source.";
            return $result;
        }

        $zip = new ZipArchive();

        if (!$zip->open($destination, ZIPARCHIVE::CREATE)) {
            $result["message"] = "Could not open destination.";
            return $result;
        }
  

        $addedFiles = array();
        $fixedFolderName = strtolower(str_replace(' ', '_' , $project->name));
        $structure = './project_sources/'.$fixedFolderName;

        foreach ($files as $f)
        {

            $completeFileName = $f['name'].".".$f['extension'];
            $filePath = $f["path"]."\\".$completeFileName;
            $repositoryPath = str_replace('file:///'.$project->root_folder_path, $structure, $filePath);

            $fileInfo = $repositoryPath;
            $file = str_replace('\\', '/', $fileInfo);

            // Ignore "." and ".." folders
            if( in_array(substr($file, strrpos($file, '/')+1), array('.', '..')) )
                continue;

            if (is_dir($file) === true)
            {
                $zip->addEmptyDir($file);
            }
            else if (is_file($file) === true)
            {

                $fullPath = $filePath;
                $newPath = str_replace($project->root_folder_path, "", $fullPath);
                $newPath = str_replace("file:///", "", $newPath);
                $fileOnlyPath = str_replace("\\", "", $newPath);
                $fileOnlyPath = str_replace("/", "", $fileOnlyPath);

                // Get tmp path relative to project folder
                $relativeTmp = $project->name."/".str_replace($project->root_folder_path, "", $project->tmp_folder_path); // Get tmp path in format projectName/path/to/tmp

                $newPathFolder = explode("\\", $newPath);
                if(count($newPathFolder)>1) { array_pop($newPathFolder); }
                $newPathFolder = implode("\\", $newPathFolder);

                // If ignore temp files checked test if it is a file from the temporary folder
                if(!$project->ignore_tmp || !$this->pathExistsIn($newPathFolder, $relativeTmp)) { 

                    $newPath = ltrim($newPath, "\\");
                    $added = $zip->addFile($file, $newPath);
                }  
                else if(!$emptyTempDir) { // Create temporary empty folder                            
                    $zip->addEmptyDir($relativeTmp);
                    $emptyTempDir = true;      
  
                  
                }                      

            }
            
        }

        if($zip->close()) {
            $result["message"] = "Zip process complete.";
            $result["status"] = "success";
        } else {
            $result["message"] = "Could not close component.";
        }

        return $result;
    }


    public function pathExistsIn($path1, $path2) {
        $path1 = str_replace("\\", "/", addslashes($path1));
        $path2 = str_replace("\\", "/", addslashes($path2));

        return strpos($path1 , $path2);

    }

    public function rrmdir($dir) {
        if (is_dir($dir)) {
            $files = scandir($dir);
            foreach ($files as $file)
                if ($file != "." && $file != "..") $this->rrmdir("$dir/$file");
            rmdir($dir);
        }
        else if (file_exists($dir)) unlink($dir);
    }
     
    public function rcopy($src, $dst) {
        if (file_exists ( $dst ))
            $this->rrmdir ( $dst );
        if (is_dir ( $src )) {
            mkdir ( $dst );
            $files = scandir ( $src );
            foreach ( $files as $file )
                if ($file != "." && $file != "..")
                    $this->rcopy ( "$src/$file", "$dst/$file" );
        } else if (file_exists ( $src ))
            copy ( $src, $dst );
    }


}
