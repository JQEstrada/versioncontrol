<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $tempfile->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $tempfile->id)]
            )
        ?></li>
        <li><?= $this->Html->link(__('List Tempfiles'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List Projects'), ['controller' => 'Projects', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Project'), ['controller' => 'Projects', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="tempfiles form large-9 medium-8 columns content">
    <?= $this->Form->create($tempfile) ?>
    <fieldset>
        <legend><?= __('Edit Tempfile') ?></legend>
        <?php
            echo $this->Form->input('project_id', ['options' => $projects]);
            echo $this->Form->input('name');
            echo $this->Form->input('path');
            echo $this->Form->input('type');
            echo $this->Form->input('extension');
            echo $this->Form->input('added_to_project');
            echo $this->Form->input('size');
            echo $this->Form->input('last_modified');
            echo $this->Form->input('system_file_created_on');
            echo $this->Form->input('origin');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
