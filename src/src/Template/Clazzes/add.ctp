<?php $this->assign('title', 'Turmas'); ?>
<?php $this->start('breadcrumb'); ?>
<li><?= $this->Html->link('<i class="fa fa-dashboard"></i>' . __('Dashboard'), '/', ['escape' => false]) ?></li>
<li><?= $this->Html->link(__('Turmas'), ['action' => 'index']) ?></li>
<li class="active">Adicionar</li>
<?php $this->end(); ?>

<?= $this->Form->create($clazz) ?>
<div class="row">
    <div class="col-xs-8">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Adicionar turma</h3>
            </div>
            <div class="box-body">
                <?php
                echo $this->Form->input('process_id', ['label' => 'Processo de distribuição', 'options' => $processes]);
                echo $this->Form->input('subject_id', ['label' => 'Disciplina', 'options' => $subjects]);
                echo $this->Form->input('name', ['label' => 'Nome da turma', 'placeholder' => 'Nome da turma']);
                echo $this->Form->input('vacancies', ['label' => 'Número de vagas', 'placeholder' => 'Número de vagas']);
                ?>
            </div>
            <div class="box-footer clearfix">
                <?= $this->Form->button(__('Salvar'), ['class' => 'btn btn-success']) ?>
            </div>
        </div>
    </div>
</div>
<?= $this->Form->end() ?>
