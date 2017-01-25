<?= $this->Html->css(['editproject']) ?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $project->id],
                ['confirm' => __('Are you sure you want to delete project {0}?', $project->name)]
            )
        ?></li>
        <li><?= $this->Html->link(__('List Projects'), ['action' => 'index']) ?></li>
    </ul>
</nav>
<div class="projects form large-9 medium-8 columns content">
    <?= $this->Form->create($project) ?>
    <fieldset>
        <legend><?= __('Edit Project') ?></legend>
        <?php
            echo $this->Form->input('name', ["readonly" => "true"]);
            echo $this->Form->input('root_folder_path', ["readonly" => "true"]);
            echo $this->Form->input('current_version', ["value" => end($project->versions)["version_number"], "type" => "text", "readonly" => "true"]);
            echo $this->Form->input('created_on', ["type" => "text", "readonly" => "true"]);
            echo $this->Form->input('ignore_tmp', ["id" => "ignore_tmp", "type" => "checkbox", "label" => "ignore temporary folder"]);
        ?>        
        <div class="ignoreinputs">
            <?php echo $this->Form->input('tmp_folder_path', ["id" => "tmp_folder_path", "label" => "Temporary Folder Path"]); ?>
        </div>
        <?php echo $this->Form->input('ftp_server', ["id" => "ftp_server", "label" => "FTP Server Address"]); ?>
        <?php echo $this->Form->input('ftp_user_name', ["id" => "ftp_user_name", "label" => "FTP Server Username"]); ?>
    </fieldset>
    <?= $this->Form->button(__('Save')) ?>
    <?= $this->Form->end() ?>
</div>
<?= $this->Html->script(['jquery-2.2.4.min']) ?>
<?= $this->Html->script(['editProject']) ?>