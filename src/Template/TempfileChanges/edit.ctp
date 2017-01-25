<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $tempfileChange->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $tempfileChange->id)]
            )
        ?></li>
        <li><?= $this->Html->link(__('List Tempfile Changes'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List Files'), ['controller' => 'Files', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New File'), ['controller' => 'Files', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Versions'), ['controller' => 'Versions', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Version'), ['controller' => 'Versions', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="tempfileChanges form large-9 medium-8 columns content">
    <?= $this->Form->create($tempfileChange) ?>
    <fieldset>
        <legend><?= __('Edit Tempfile Change') ?></legend>
        <?php
            echo $this->Form->input('file_id', ['options' => $files]);
            echo $this->Form->input('version_id', ['options' => $versions]);
            echo $this->Form->input('action');
            echo $this->Form->input('datetime');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
