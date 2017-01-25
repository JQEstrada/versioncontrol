<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Datasource\ConnectionManager;

/**
 * Projects Controller
 *
 * @property \App\Model\Table\ProjectsTable $Projects
 */
class DatabaseController extends AppController
{
    var $uses = false;
    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index()
    {


    }

    public function createdb() {

      $conn = ConnectionManager::get('default');

      $stmt = $conn->query("CREATE DATABASE IF NOT EXISTS versioncontrol");
      $stmt->closeCursor();
      $check = $conn->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = 'versioncontrol'");

      if(count($check->fetchAll('assoc'))) {



          $createFullDB = $conn->query('
            USE versioncontrol;
            SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
            SET time_zone = "+00:00";
            CREATE TABLE `files` (
              `id` int(7) NOT NULL,
              `project_id` int(7) NOT NULL,
              `name` varchar(200) COLLATE utf8_swedish_ci NOT NULL,
              `path` varchar(200) CHARACTER SET latin1 NOT NULL,
              `type` varchar(30) CHARACTER SET latin1 NOT NULL,
              `extension` varchar(6) CHARACTER SET latin1 NOT NULL,
              `added_to_project` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
              `size` int(20) NOT NULL,
              `last_modified` datetime NOT NULL,
              `system_file_created_on` datetime NOT NULL,
              `origin` varchar(25) CHARACTER SET latin1 NOT NULL DEFAULT "database",
              `commited` tinyint(1) NOT NULL DEFAULT "0",
              `deleted` tinyint(1) NOT NULL DEFAULT "0",
              `pushed` tinyint(1) NOT NULL DEFAULT "0"
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;
            CREATE TABLE `file_changes` (
              `id` int(7) NOT NULL,
              `file_id` int(7) NOT NULL,
              `version_id` int(7) NOT NULL,
              `action` varchar(20) NOT NULL,
              `datetime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=latin1;
            CREATE TABLE `procedures` (
              `id` int(7) NOT NULL,
              `project_id` int(7) NOT NULL,
              `current_procedure` varchar(50) NOT NULL,
              `due_procedure` varchar(50) NOT NULL,
              `is_complete` tinyint(1) NOT NULL DEFAULT "0",
              `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
              `handshake_code` varchar(100) NOT NULL,
              `stepsCompleted` int(2) NOT NULL DEFAULT "0",
              `stepsToGo` int(2) NOT NULL DEFAULT "0"
            ) ENGINE=InnoDB DEFAULT CHARSET=latin1;

            CREATE TABLE `projects` (
              `id` int(7) NOT NULL,
              `name` varchar(50) NOT NULL,
              `root_folder_path` varchar(200) NOT NULL,
              `last_change` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
              `current_version_id` int(7) NOT NULL DEFAULT "1",
              `archive_folder_path` varchar(2000) NOT NULL,
              `created_on` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
              `physicalfiles` text NOT NULL,
              `ignore_tmp` tinyint(1) NOT NULL DEFAULT "0",
              `handshake_code` varchar(50) NOT NULL,
              `copy_first_version_to_repository` tinyint(1) NOT NULL,
              `transfer_type` varchar(50) NOT NULL,
              `tmp_folder_path` varchar(200) NOT NULL,
              `ftp_server` varchar(100) NOT NULL,
              `ftp_user_name` varchar(100) NOT NULL,
              `ftp_password` varchar(100) NOT NULL,
              `mark_first_push` tinyint(1) NOT NULL DEFAULT "0",
              `deleted` tinyint(1) NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=latin1;
            CREATE TABLE `tempfiles` (
              `id` int(7) NOT NULL,
              `project_id` int(7) NOT NULL,
              `name` varchar(200) NOT NULL,
              `path` varchar(200) NOT NULL,
              `type` varchar(30) NOT NULL,
              `extension` varchar(6) NOT NULL,
              `added_to_project` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
              `size` int(20) NOT NULL,
              `last_modified` datetime NOT NULL,
              `system_file_created_on` datetime NOT NULL,
              `origin` varchar(25) NOT NULL DEFAULT "real_path"
            ) ENGINE=InnoDB DEFAULT CHARSET=latin1;
            CREATE TABLE `tempfile_changes` (
              `id` int(7) NOT NULL,
              `fileid` int(7) NOT NULL,
              `action` varchar(20) NOT NULL,
              `datetime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
              `procedure_handshake` varchar(100) NOT NULL,
              `name` varchar(200) NOT NULL,
              `path` varchar(200) NOT NULL,
              `type` varchar(30) NOT NULL,
              `extension` varchar(6) NOT NULL,
              `size` int(20) NOT NULL,
              `lastModified` datetime NOT NULL,
              `procedure_id` int(7) NOT NULL,
              `topval` datetime NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=latin1;
            CREATE TABLE `v` (
            `fileName` varchar(200)
            ,`projectId` int(11)
            ,`filePath` varchar(200)
            ,`fileType` varchar(30)
            ,`fileExtension` varchar(6)
            ,`fileAddedToProject` datetime
            ,`fileSize` int(20)
            ,`fileLastModified` datetime
            ,`fileSystemCreatedOn` datetime
            ,`fileOrigin` varchar(25)
            ,`fileId` int(11)
            ,`fileDeleted` bigint(20)
            );
            CREATE TABLE `versions` (
              `id` int(7) NOT NULL,
              `project_id` int(7) NOT NULL,
              `archive_folder_present` varchar(3) NOT NULL DEFAULT "No",
              `archive_folder_path` varchar(100) NOT NULL,
              `created_on` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
              `version_number` varchar(7) NOT NULL,
              `is_current_version` varchar(3) NOT NULL DEFAULT "yes",
              `comments` text NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=latin1;
            DROP TABLE IF EXISTS `v`;

            CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v`  AS  select `files`.`name` AS `fileName`,`files`.`project_id` AS `projectId`,`files`.`path` AS `filePath`,`files`.`type` AS `fileType`,`files`.`extension` AS `fileExtension`,`files`.`added_to_project` AS `fileAddedToProject`,`files`.`size` AS `fileSize`,`files`.`last_modified` AS `fileLastModified`,`files`.`system_file_created_on` AS `fileSystemCreatedOn`,`files`.`origin` AS `fileOrigin`,`files`.`id` AS `fileId`,`files`.`deleted` AS `fileDeleted` from `files` union select `tempfiles`.`name` AS `fileName`,`tempfiles`.`project_id` AS `projectId`,`tempfiles`.`path` AS `filePath`,`tempfiles`.`type` AS `fileType`,`tempfiles`.`extension` AS `fileExtension`,`tempfiles`.`added_to_project` AS `fileAddedToProject`,`tempfiles`.`size` AS `fileSize`,`tempfiles`.`last_modified` AS `fileLastModified`,`tempfiles`.`system_file_created_on` AS `fileSystemCreatedOn`,`tempfiles`.`origin` AS `fileOrigin`,`tempfiles`.`id` AS `fileId`,0 AS `fileDeleted` from `tempfiles` ;
            ALTER TABLE `files`
              ADD PRIMARY KEY (`id`),
              ADD KEY `name` (`name`,`last_modified`);
            ALTER TABLE `file_changes`
              ADD PRIMARY KEY (`id`);
            ALTER TABLE `procedures`
              ADD PRIMARY KEY (`id`);
            ALTER TABLE `projects`
              ADD PRIMARY KEY (`id`);
            ALTER TABLE `tempfiles`
              ADD PRIMARY KEY (`id`),
              ADD KEY `name` (`name`,`last_modified`);
            ALTER TABLE `tempfile_changes`
              ADD PRIMARY KEY (`id`);
            ALTER TABLE `versions`
              ADD PRIMARY KEY (`id`);
            ALTER TABLE `files`
              MODIFY `id` int(7) NOT NULL AUTO_INCREMENT;
            ALTER TABLE `file_changes`
              MODIFY `id` int(7) NOT NULL AUTO_INCREMENT;
            ALTER TABLE `procedures`
              MODIFY `id` int(7) NOT NULL AUTO_INCREMENT;
            ALTER TABLE `projects`
              MODIFY `id` int(7) NOT NULL AUTO_INCREMENT;
            ALTER TABLE `tempfiles`
              MODIFY `id` int(7) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9132;
            ALTER TABLE `tempfile_changes`
              MODIFY `id` int(7) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=153;
            ALTER TABLE `versions`
              MODIFY `id` int(7) NOT NULL AUTO_INCREMENT;');
          
        $check->closeCursor();
        $checkTable = $conn->query("SELECT * FROM information_schema.tables WHERE table_schema = 'versioncontrol' AND table_name = 'versions' LIMIT 1;");

        if(count($checkTable->fetchAll('assoc'))) {

            $this->Flash->success(__('Database configured. You can start using the app! '));

        } else {

            $this->Flash->error(__('Could not create Database. '));

        }
      } else {
          $this->Flash->error(__('Could not create Database. '));
      }

      return $this->redirect(['action' => 'index']);
      
  }



}
