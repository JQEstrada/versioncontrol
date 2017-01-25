<?= $this->Html->css(['viewproject']) ?>
<?= $this->Html->css(['css/bootstrap']) ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/themes/default/style.min.css" />
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Project'), ['action' => 'edit', $project->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Project'), ['action' => 'delete', $project->id], ['confirm' => __('Are you sure you want to delete project {0}?', $project->name)]) ?> </li>
        <li><?= $this->Html->link(__('List Projects'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Project'), ['action' => 'add']) ?> </li>
    </ul>
</nav>

<div class="projects view large-9 medium-8 columns content">
    <h3><?= h($project->name) ?></h3>

    <fieldset>
        <legend>Changes</legend>
        <div class="loaderDiv"><img src="/versioncontrol/img/ajax-loader.gif" /><span style="margin-left: 5px">Looking for changes in project...</span></div><span id="commitFeedback"></span>
        <div class="commitButtons">
            <?= $this->Form->button(__('Commit Changes'), ['id'=>'commitBtn']) ?>
        </div>

        <div class="commitInfo">
            <div class="commitCommentsDiv"> 
                <?php echo $this->Form->input('commit_comments'); ?>
            </div>
            <div class="leftColumn">
                <div id="jstree">
                </div>
            </div>
            <div class="rightColumn">
                <div class="buttonsDivs">
                    <div class="buttonDiv"><?= $this->Form->button(__('Confirm Commit'), ['id'=>'confirmCommit', 'style' => 'width:200px']) ?></div>
                    <div class="buttonDiv"><?= $this->Form->button(__('Commit & Push'), ['id'=>'pushBtn', 'style' => 'width:200px']) ?></div>
                    <div class="buttonDiv"><?= $this->Form->button(__('Refresh'), ['id'=>'refreshCommit', 'style' => 'width:200px']) ?></div>
                </div>
                <div class="credentialsDiv">
                    <fieldset id="fileTransferFTP" class="fileTransferFields">
                        <legend>FTP Transfer</legend>
                        <p>
                        Please fill information of the project remote destination
                        </p> 
                        <?php 
                            echo $this->Form->input('ftp_server');
                            echo $this->Form->input('ftp_user_name');
                            echo $this->Form->input('ftp_password');
                            echo $this->Form->button(__('Confirm Push'), ['id'=>'confirmPush', 'style' => 'width:200px']);
                            echo $this->Form->button(__('Back'), ['id'=>'back', 'style' => 'width:200px']);
                        ?>
                    </fieldset>
                </div>
            </div>
        </div>

    </fieldset>

    <fieldset>
        <legend>Files to Push</legend>

        <div class="loaderDivPush"><img src="/versioncontrol/img/ajax-loader.gif" /><span style="margin-left: 5px">Looking for files to push in project...</span></div><span id="pushFeedback"></span>
        <div class="pushButtons">
            <?= $this->Form->button(__('Look for files to Push'), ['id'=>'searchPushBtn']) ?>
        </div>

        <table id="pushTable" cellpadding="0" cellspacing="0">
            <tr>
                <th scope="col"><?= __('Path') ?></th>
                <th scope="col"><?= __('File Name') ?></th>
                <th scope="col"><?= __('Extension') ?></th>
                <th scope="col"><?= __('Last Change') ?></th>
                <th scope="col"><input type="checkbox" id="selectAllPushes" /></th>
            </tr>
        </table>
        <div class="credentialsDivPush">
            <fieldset id="fileTransferFTPPush" class="fileTransferFieldsPush">
                <legend>FTP Transfer</legend>
                <p>
                Please fill information of the project remote destination
                </p> 
                <?php 
                    echo $this->Form->input('ftp_server_push');
                    echo $this->Form->input('ftp_user_name_push');
                    echo $this->Form->input('ftp_password_push');
                    echo $this->Form->button(__('Confirm Push'), ['id'=>'confirmIndividualPush', 'style' => 'width:200px']);
                    echo $this->Form->button(__('Back'), ['id'=>'backPush', 'style' => 'width:200px']);
                ?>
            </fieldset>
        </div>
    </fieldset>


    <fieldset>
        <legend>Details<span class="editSpan"><a href="/versioncontrol/projects/edit/<?php echo $project->id ?>">Edit<i class="glyphicon glyphicon-edit"></i></a></span></legend>
        <table class="vertical-table">
            <tr>
                <th scope="row"><?= __('Root Folder Path') ?></th>
                <td><?= h($project->root_folder_path) ?></td>
            </tr>            <tr>
                <th scope="row"><?= __('Current Version') ?></th>
                <td><?= end($project->versions)["version_number"] ?></td>
            </tr>
            <tr>
                <th scope="row"><?= __('Last Change') ?></th>
                <td><?= h($project->last_change) ?></td>
            </tr>
            <tr>
                <th scope="row"><?= __('Created On') ?></th>
                <td><?= h($project->created_on) ?></td>
            </tr>
            <tr>
                <th scope="row"><?= __('FTP Server Address') ?></th>
                <td><?= h($project->ftp_server) ?></td>
            </tr>
            <tr>
                <th scope="row"><?= __('FTP Server Username') ?></th>
                <td><?= h($project->ftp_user_name) ?></td>
            </tr>
        </table>
    </fieldset>
    <div class="related">
        <h4><?= __('Related Files') ?></h4>
        <?php if (!empty($project->files)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th scope="col"><?= __('Path') ?></th>
                <th scope="col"><?= __('File Name') ?></th>
                <th scope="col"><?= __('Extension') ?></th>
                <th scope="col"><?= __('Last Change') ?></th>
            </tr>
            <?php foreach ($project->files as $files): ?>
            <tr>
                <td><?= h($files->path) ?></td>
                <td><?= h($files->name) ?></td>
                <td><?= h($files->extension) ?></td>
                <td><?= h($files->last_modified) ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>
</div>
<script>
var projectId = '<?php echo $project->id; ?>';
var projectFtpServer = '<?php echo $project->ftp_server; ?>';
var projectFtpUser = '<?php echo $project->ftp_user_name; ?>'; 
</script>
<?= $this->Html->script(['jquery-2.2.4.min']) ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/jstree.min.js"></script>
<?= $this->Html->script(['viewProject']) ?>