<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('New Procedure'), ['action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Projects'), ['controller' => 'Projects', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Project'), ['controller' => 'Projects', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="procedures index large-9 medium-8 columns content">
    <h3><?= __('Procedures') ?></h3>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th scope="col"><?= $this->Paginator->sort('id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('project_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('current_procedure') ?></th>
                <th scope="col"><?= $this->Paginator->sort('due_procedure') ?></th>
                <th scope="col"><?= $this->Paginator->sort('is_complete') ?></th>
                <th scope="col"><?= $this->Paginator->sort('last_change') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($procedures as $procedure): ?>
            <tr>
                <td><?= $this->Number->format($procedure->id) ?></td>
                <td><?= $procedure->has('project') ? $this->Html->link($procedure->project->id, ['controller' => 'Projects', 'action' => 'view', $procedure->project->id]) : '' ?></td>
                <td><?= h($procedure->current_procedure) ?></td>
                <td><?= h($procedure->due_procedure) ?></td>
                <td><?= h($procedure->is_complete) ?></td>
                <td><?= h($procedure->last_change) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['action' => 'view', $procedure->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $procedure->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $procedure->id], ['confirm' => __('Are you sure you want to delete # {0}?', $procedure->id)]) ?>
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
