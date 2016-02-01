<?php $this->assign('title', 'Turmas'); ?>
<?php $this->start('breadcrumb'); ?>
<li><?= $this->Html->link('<i class="fa fa-dashboard"></i>' . __('Dashboard'), '/', ['escape' => false]) ?></li>
<li class="active">Lista de Turmas</li>
<?php $this->end(); ?>

<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">
            <div class="box-header">
                <div class="pull-right box-tools">
                    <?= $this->Html->link(
                        '<i class="fa fa-plus-circle"></i> ' . __('Adicionar'),
                        ['action' => 'add'],
                        [
                            'escape' => false,
                            'data-toggle' => 'tooltip',
                            'data-original-title' => __('Adicionar'),
                            'class' => 'btn btn-sm btn-primary'
                        ]
                    );
                    ?>
                </div>
            </div>
            <div class="box-body table-responsive no-padding">
                <table class="table table-striped table-valign-middle">
                    <thead>
                    <tr>
                        <th><?= $this->Paginator->sort('id', __('#ID')) ?></th>
                        <th><?= $this->Paginator->sort('name', __('Nome')) ?></th>
                        <th><?= $this->Paginator->sort('vacancies', __('Vagas')) ?></th>
                        <th width="200px"><?= __('Ações') ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if($clazzes->isEmpty()): ?>
                        <tr>
                            <td colspan="3" class="text-center">Não existe nenhuma turma cadastrada</td>
                        </tr>
                    <?php endif; ?>

                    <?php foreach ($clazzes as $clazze): ?>
                        <tr>
                            <td><?= $this->Number->format($clazze->id) ?></td>
                            <td><?= h($clazze->name) ?></td>
                            <td><?= $this->Number->format($clazze->vacancies) ?></td>
                            <td>
                                <?= $this->Html->link(
                                    '',
                                    ['action' => 'view', $clazze->id],
                                    [
                                        'title' => __('Visualizar'),
                                        'class' => 'btn btn-sm btn-default glyphicon glyphicon-search',
                                        'data-toggle' => 'tooltip',
                                        'data-original-title' => __('Visualizar'),
                                    ]
                                ) ?>
                                <?= $this->Html->link(
                                    '',
                                    ['action' => 'edit', $clazze->id],
                                    [
                                        'title' => __('Editar'),
                                        'class' => 'btn btn-sm btn-primary glyphicon glyphicon-pencil',
                                        'data-toggle' => 'tooltip',
                                        'data-original-title' => __('Editar'),
                                    ]
                                ) ?>
                                <?= $this->Form->postLink(
                                    '',
                                    ['action' => 'delete', $clazze->id],
                                    [
                                        'confirm' => __('Você tem certeza de que deseja remover a turma "{0}"?', $clazze->name),
                                        'title' => __('Remover'),
                                        'class' => 'btn btn-sm btn-danger glyphicon glyphicon-trash',
                                        'data-toggle' => 'tooltip',
                                        'data-original-title' => __('Remover'),
                                    ]
                                ) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="box-footer clearfix">
                <ul class="pagination pagination-sm no-margin pull-right">
                    <?= $this->Paginator->prev('«') ?>
                    <?= $this->Paginator->numbers() ?>
                    <?= $this->Paginator->next('»') ?>
                </ul>
            </div>
        </div>
    </div>
</div>
