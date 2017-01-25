<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit File Change'), ['action' => 'edit', $fileChange->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete File Change'), ['action' => 'delete', $fileChange->id], ['confirm' => __('Are you sure you want to delete # {0}?', $fileChange->id)]) ?> </li>
        <li><?= $this->Html->link(__('List File Changes'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New File Change'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Files'), ['controller' => 'Files', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New File'), ['controller' => 'Files', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Versions'), ['controller' => 'Versions', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Version'), ['controller' => 'Versions', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="fileChanges view large-9 medium-8 columns content">
    <h3><?= h($fileChange->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('File') ?></th>
            <td><?= $fileChange->has('file') ? $this->Html->link($fileChange->file->id, ['controller' => 'Files', 'action' => 'view', $fileChange->file->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Version') ?></th>
            <td><?= $fileChange->has('version') ? $this->Html->link($fileChange->version->id, ['controller' => 'Versions', 'action' => 'view', $fileChange->version->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Action') ?></th>
            <td><?= h($fileChange->action) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($fileChange->id) ?></td>
        </tr>
    </table>
</div>
