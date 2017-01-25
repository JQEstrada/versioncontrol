<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Version'), ['action' => 'edit', $version->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Version'), ['action' => 'delete', $version->id], ['confirm' => __('Are you sure you want to delete # {0}?', $version->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Versions'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Version'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Projects'), ['controller' => 'Projects', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Project'), ['controller' => 'Projects', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List File Changes'), ['controller' => 'FileChanges', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New File Change'), ['controller' => 'FileChanges', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="versions view large-9 medium-8 columns content">
    <h3><?= h($version->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Project') ?></th>
            <td><?= $version->has('project') ? $this->Html->link($version->project->id, ['controller' => 'Projects', 'action' => 'view', $version->project->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Archive Folder Present') ?></th>
            <td><?= h($version->archive_folder_present) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Archive Folder Path') ?></th>
            <td><?= h($version->archive_folder_path) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($version->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Created On') ?></th>
            <td><?= h($version->created_on) ?></td>
        </tr>
    </table>
    <div class="related">
        <h4><?= __('Related File Changes') ?></h4>
        <?php if (!empty($version->file_changes)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th scope="col"><?= __('Id') ?></th>
                <th scope="col"><?= __('File Id') ?></th>
                <th scope="col"><?= __('Version Id') ?></th>
                <th scope="col"><?= __('Action') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($version->file_changes as $fileChanges): ?>
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
