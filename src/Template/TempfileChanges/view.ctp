<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Tempfile Change'), ['action' => 'edit', $tempfileChange->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Tempfile Change'), ['action' => 'delete', $tempfileChange->id], ['confirm' => __('Are you sure you want to delete # {0}?', $tempfileChange->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Tempfile Changes'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Tempfile Change'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Files'), ['controller' => 'Files', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New File'), ['controller' => 'Files', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Versions'), ['controller' => 'Versions', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Version'), ['controller' => 'Versions', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="tempfileChanges view large-9 medium-8 columns content">
    <h3><?= h($tempfileChange->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('File') ?></th>
            <td><?= $tempfileChange->has('file') ? $this->Html->link($tempfileChange->file->id, ['controller' => 'Files', 'action' => 'view', $tempfileChange->file->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Version') ?></th>
            <td><?= $tempfileChange->has('version') ? $this->Html->link($tempfileChange->version->id, ['controller' => 'Versions', 'action' => 'view', $tempfileChange->version->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Action') ?></th>
            <td><?= h($tempfileChange->action) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($tempfileChange->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Datetime') ?></th>
            <td><?= h($tempfileChange->datetime) ?></td>
        </tr>
    </table>
</div>
