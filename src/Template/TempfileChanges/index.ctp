<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('New Tempfile Change'), ['action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Files'), ['controller' => 'Files', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New File'), ['controller' => 'Files', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Versions'), ['controller' => 'Versions', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Version'), ['controller' => 'Versions', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="tempfileChanges index large-9 medium-8 columns content">
    <h3><?= __('Tempfile Changes') ?></h3>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th scope="col"><?= $this->Paginator->sort('id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('file_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('version_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('action') ?></th>
                <th scope="col"><?= $this->Paginator->sort('datetime') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($tempfileChanges as $tempfileChange): ?>
            <tr>
                <td><?= $this->Number->format($tempfileChange->id) ?></td>
                <td><?= $tempfileChange->has('file') ? $this->Html->link($tempfileChange->file->id, ['controller' => 'Files', 'action' => 'view', $tempfileChange->file->id]) : '' ?></td>
                <td><?= $tempfileChange->has('version') ? $this->Html->link($tempfileChange->version->id, ['controller' => 'Versions', 'action' => 'view', $tempfileChange->version->id]) : '' ?></td>
                <td><?= h($tempfileChange->action) ?></td>
                <td><?= h($tempfileChange->datetime) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['action' => 'view', $tempfileChange->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $tempfileChange->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $tempfileChange->id], ['confirm' => __('Are you sure you want to delete # {0}?', $tempfileChange->id)]) ?>
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
