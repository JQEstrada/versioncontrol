<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Tempfile'), ['action' => 'edit', $tempfile->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Tempfile'), ['action' => 'delete', $tempfile->id], ['confirm' => __('Are you sure you want to delete # {0}?', $tempfile->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Tempfiles'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Tempfile'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Projects'), ['controller' => 'Projects', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Project'), ['controller' => 'Projects', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="tempfiles view large-9 medium-8 columns content">
    <h3><?= h($tempfile->name) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Project') ?></th>
            <td><?= $tempfile->has('project') ? $this->Html->link($tempfile->project->id, ['controller' => 'Projects', 'action' => 'view', $tempfile->project->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Name') ?></th>
            <td><?= h($tempfile->name) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Path') ?></th>
            <td><?= h($tempfile->path) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Type') ?></th>
            <td><?= h($tempfile->type) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Extension') ?></th>
            <td><?= h($tempfile->extension) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Origin') ?></th>
            <td><?= h($tempfile->origin) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($tempfile->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Size') ?></th>
            <td><?= $this->Number->format($tempfile->size) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Added To Project') ?></th>
            <td><?= h($tempfile->added_to_project) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Last Modified') ?></th>
            <td><?= h($tempfile->last_modified) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('System File Created On') ?></th>
            <td><?= h($tempfile->system_file_created_on) ?></td>
        </tr>
    </table>
</div>
