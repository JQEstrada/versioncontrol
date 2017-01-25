<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $procedure->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $procedure->id)]
            )
        ?></li>
        <li><?= $this->Html->link(__('List Procedures'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List Projects'), ['controller' => 'Projects', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Project'), ['controller' => 'Projects', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="procedures form large-9 medium-8 columns content">
    <?= $this->Form->create($procedure) ?>
    <fieldset>
        <legend><?= __('Edit Procedure') ?></legend>
        <?php
            echo $this->Form->input('project_id', ['options' => $projects]);
            echo $this->Form->input('current_procedure');
            echo $this->Form->input('due_procedure');
            echo $this->Form->input('is_complete');
            echo $this->Form->input('last_change');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
