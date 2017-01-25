<?= $this->Html->css(['addproject']) ?>
<?= $this->Html->css(['css/bootstrap']) ?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('List Projects'), ['action' => 'index']) ?></li>
    </ul>
</nav>
<div class="projects form large-9 medium-8 columns content">
    <?= $this->Form->create($project, ['type'=>'file', "id" => "addProjectForm"]) ?>
    <fieldset>
        <legend><?= __('Add Project') ?></legend>
        <?php
            echo $this->Form->input('name', ["placeholder" => "Unique name for your project"]);
            echo $this->Form->input('root_folder_path',["label" => "Root Folder Path (ex: C:/xampp/htdocs/project/)", "placeholder" => "example: C:/xampp/htdocs/myproject"]);
            echo $this->Form->input('ignore_tmp', ["id" => "ignore_tmp", "type" => "checkbox", "label" => "ignore temporary folder"]);
            echo $this->Form->hidden('handshake_code', ["id" => "handshake_code"]);
        ?>
        <div class="ignoreinputs">
            <input placeholder="path to temporary folder" type="text" name="tmp_folder_path" id="tmp_folder_path"/>
        </div>
        <?php
            echo $this->Form->input('copy_first_version_to_repository', array(
                    'type' => 'select',
                    'id' => 'copy_first_version_to_repository',
                    'label' => '',
                    'multiple' => 'checkbox',
                    'options' => array(
                            '1' => 'copy to repository'
                    )
                ));
        ?>  
        <div class="radioinputs">
            <input type="radio" class="transfer_type" name="transfer_type" value="zip_download" id="zip_download"><label for="zip_download">Download Zip File</label>
            <input type="radio" class="transfer_type" name="transfer_type" value="ftp_transfer" id="ftp_transfer"><label for="ftp_transfer">Send by FTP</label>
        </div>
    </fieldset>

    <fieldset id="fileTransferZip" class="fileTransferFields">
        <legend>Compress project folder (recommended for large projects)</legend>
       <p>
       When the project is saved you can click to download the files. You can use them to update your project on a remote server.
       <?php echo $this->Form->input('mark_first_push', ["id" => "mark_first_push", "type" => "checkbox", "label" => "Mark files as pushed to remote server", "value" => "1"]); ?>
       </p>
    </fieldset>

    <fieldset id="fileTransferFTP" class="fileTransferFields">
        <legend>FTP Transfer</legend>
        <p>
        Please fill information of the project remote destination
        </p> 
        <?php 
            echo $this->Form->input('ftp_server');
            echo $this->Form->input('ftp_user_name');
            echo $this->Form->input('ftp_password');
        ?>
    </fieldset>
    <?php //$this->Form->button(__('Submit')) ?>
    <div style="text-align:center;width:100%;padding-right:5%"><button type="button" id="createBtn">Create</button></div>
    <?= $this->Form->end() ?>
    <div id="downloadDiv">
    <?php echo $this->Html->link("Download Zip File", array('controller' => 'fileapi', 'action' => 'download', 'handshake') ); ?>
    </div>
    <div id="progressDiv">
        <div class="progressInfo">
        </div>
        <div id="myProgress">
          <div id="myEffectBar"></div>
          <div id="myBar"></div>
        </div>
    </div>
</div>
<?= $this->Html->script(['jquery-2.2.4.min']) ?>
<?= $this->Html->script(['addNewProject']) ?>