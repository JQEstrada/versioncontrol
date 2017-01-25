<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('New Tempfile'), ['action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Projects'), ['controller' => 'Projects', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Project'), ['controller' => 'Projects', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="tempfiles index large-9 medium-8 columns content">
    <h3><?= __('Tempfiles') ?></h3>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th scope="col"><?= $this->Paginator->sort('id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('project_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('name') ?></th>
                <th scope="col"><?= $this->Paginator->sort('path') ?></th>
                <th scope="col"><?= $this->Paginator->sort('type') ?></th>
                <th scope="col"><?= $this->Paginator->sort('extension') ?></th>
                <th scope="col"><?= $this->Paginator->sort('added_to_project') ?></th>
                <th scope="col"><?= $this->Paginator->sort('size') ?></th>
                <th scope="col"><?= $this->Paginator->sort('last_modified') ?></th>
                <th scope="col"><?= $this->Paginator->sort('system_file_created_on') ?></th>
                <th scope="col"><?= $this->Paginator->sort('origin') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($tempfiles as $tempfile): ?>
            <tr>
                <td><?= $this->Number->format($tempfile->id) ?></td>
                <td><?= $tempfile->has('project') ? $this->Html->link($tempfile->project->id, ['controller' => 'Projects', 'action' => 'view', $tempfile->project->id]) : '' ?></td>
                <td><?= h($tempfile->name) ?></td>
                <td><?= h($tempfile->path) ?></td>
                <td><?= h($tempfile->type) ?></td>
                <td><?= h($tempfile->extension) ?></td>
                <td><?= h($tempfile->added_to_project) ?></td>
                <td><?= $this->Number->format($tempfile->size) ?></td>
                <td><?= h($tempfile->last_modified) ?></td>
                <td><?= h($tempfile->system_file_created_on) ?></td>
                <td><?= h($tempfile->origin) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['action' => 'view', $tempfile->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $tempfile->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $tempfile->id], ['confirm' => __('Are you sure you want to delete # {0}?', $tempfile->id)]) ?>
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
