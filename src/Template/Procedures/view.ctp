<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Procedure'), ['action' => 'edit', $procedure->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Procedure'), ['action' => 'delete', $procedure->id], ['confirm' => __('Are you sure you want to delete # {0}?', $procedure->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Procedures'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Procedure'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Projects'), ['controller' => 'Projects', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Project'), ['controller' => 'Projects', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="procedures view large-9 medium-8 columns content">
    <h3><?= h($procedure->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Project') ?></th>
            <td><?= $procedure->has('project') ? $this->Html->link($procedure->project->id, ['controller' => 'Projects', 'action' => 'view', $procedure->project->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Current Procedure') ?></th>
            <td><?= h($procedure->current_procedure) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Due Procedure') ?></th>
            <td><?= h($procedure->due_procedure) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($procedure->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Last Change') ?></th>
            <td><?= h($procedure->last_change) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Is Complete') ?></th>
            <td><?= $procedure->is_complete ? __('Yes') : __('No'); ?></td>
        </tr>
    </table>
</div>
