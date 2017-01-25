<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('New Version'), ['action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Projects'), ['controller' => 'Projects', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Project'), ['controller' => 'Projects', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List File Changes'), ['controller' => 'FileChanges', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New File Change'), ['controller' => 'FileChanges', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="versions index large-9 medium-8 columns content">
    <h3><?= __('Versions') ?></h3>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th scope="col"><?= $this->Paginator->sort('id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('project_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('archive_folder_present') ?></th>
                <th scope="col"><?= $this->Paginator->sort('archive_folder_path') ?></th>
                <th scope="col"><?= $this->Paginator->sort('created_on') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($versions as $version): ?>
            <tr>
                <td><?= $this->Number->format($version->id) ?></td>
                <td><?= $version->has('project') ? $this->Html->link($version->project->id, ['controller' => 'Projects', 'action' => 'view', $version->project->id]) : '' ?></td>
                <td><?= h($version->archive_folder_present) ?></td>
                <td><?= h($version->archive_folder_path) ?></td>
                <td><?= h($version->created_on) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['action' => 'view', $version->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $version->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $version->id], ['confirm' => __('Are you sure you want to delete # {0}?', $version->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div class="paginator">
        <ul class="pagination">
            <?= $this->Paginator->first('<< ' . __('first')) ?>
            <?= $this->Paginator->prev('< ' . __('previous')) ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next(__('next') . ' >') ?>
            <?= $this->Paginator->last(__('last') . ' >>') ?>
        </ul>
        <p><?= $this->Paginator->counter(['format' => __('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')]) ?></p>
    </div>
</div>
