<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('List Versions'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List Projects'), ['controller' => 'Projects', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Project'), ['controller' => 'Projects', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List File Changes'), ['controller' => 'FileChanges', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New File Change'), ['controller' => 'FileChanges', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="versions form large-9 medium-8 columns content">
    <?= $this->Form->create($version) ?>
    <fieldset>
        <legend><?= __('Add Version') ?></legend>
        <?php
            echo $this->Form->input('project_id', ['options' => $projects]);
            echo $this->Form->input('archive_folder_present');
            echo $this->Form->input('archive_folder_path');
            echo $this->Form->input('created_on');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
