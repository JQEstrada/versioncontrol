<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit File'), ['action' => 'edit', $file->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete File'), ['action' => 'delete', $file->id], ['confirm' => __('Are you sure you want to delete # {0}?', $file->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Files'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New File'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Projects'), ['controller' => 'Projects', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Project'), ['controller' => 'Projects', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List File Changes'), ['controller' => 'FileChanges', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New File Change'), ['controller' => 'FileChanges', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="files view large-9 medium-8 columns content">
    <h3><?= h($file->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Project') ?></th>
            <td><?= $file->has('project') ? $this->Html->link($file->project->id, ['controller' => 'Projects', 'action' => 'view', $file->project->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Path') ?></th>
            <td><?= h($file->path) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Type') ?></th>
            <td><?= h($file->type) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Extension') ?></th>
            <td><?= h($file->extension) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($file->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Last Change') ?></th>
            <td><?= h($file->last_change) ?></td>
        </tr>
    </table>
    <div class="related">
        <h4><?= __('Related File Changes') ?></h4>
        <?php if (!empty($file->file_changes)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th scope="col"><?= __('Id') ?></th>
                <th scope="col"><?= __('File Id') ?></th>
                <th scope="col"><?= __('Version Id') ?></th>
                <th scope="col"><?= __('Action') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($file->file_changes as $fileChanges): ?>
            <tr>
                <td><?= h($fileChanges->id) ?></td>
                <td><?= h($fileChanges->file_id) ?></td>
                <td><?= h($fileChanges->version_id) ?></td>
                <td><?= h($fileChanges->action) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'FileChanges', 'action' => 'view', $fileChanges->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['controller' => 'FileChanges', 'action' => 'edit', $fileChanges->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'FileChanges', 'action' => 'delete', $fileChanges->id], ['confirm' => __('Are you sure you want to delete # {0}?', $fileChanges->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>
</div>
