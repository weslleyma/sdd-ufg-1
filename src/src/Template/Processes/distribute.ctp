<?php $this->assign('title', 'Distribuição automática - Página Inicial'); ?>
<?php $this->start('breadcrumb'); ?>
    <li><?= $this->Html->link('<i class="fa fa-dashboard"></i>' . __('Dashboard'), '/', ['escape' => false]) ?></li>
    <li class="active">Distribuição automática - Página Inicial</li>
<?php $this->end(); ?>

<!-- FIRST TABLE -->
<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title">Disciplinas já alocadas e não conflitantes</h3>
            </div>
            <div class="box-body table-responsive no-padding">
                <table class="table table-striped table-valign-middle">
                    <thead>
                        <tr>
                            <th><?= $this->Paginator->sort('codDisciplina', __('#CodDisciplina')) ?></th>
                            <th><?= $this->Paginator->sort('disciplina', __('Disciplina')) ?></th>
                            <th><?= $this->Paginator->sort('matricula', __('Matrícula')) ?></th>
                            <th><?= $this->Paginator->sort('docente', __('Docente')) ?></th>
							<th><?= $this->Paginator->sort('local', __('Local')) ?></th>
							<th><?= $this->Paginator->sort('codHorario', __('#CodHorario')) ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($allocatedAndNonConflictingClazzes as $clazzId => $clazzInfo): ?>
                            <tr>
                                <td><?= $this->Number->format($clazzId) ?></td>
                                <td><?= h($clazzInfo['subjectName']) ?></td>
                                <td><?= h($clazzInfo['teacherRegistry']) ?></td>
                                <td><?= h($clazzInfo['userName']) ?></td>
                                <td><?= h($clazzInfo['locals']) ?></td>
                                <td><?= h($clazzInfo['schedules']) ?></td>
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

<!-- SECOND AND THIRD TABLES -->
<div class="row">
    <div class="col-xs-6">
        <div class="box box-solid">
            <div class="box-header">
                <h3 class="box-title">Turmas ainda não alocadas</h3>
            </div>
            <div class="box-body table-responsive no-padding">
                <table class="table table-striped table-valign-middle">
                    <thead>
                        <tr>
                            <th><?= $this->Paginator->sort('codDisciplina', __('#CodDisciplina')) ?></th>
                            <th><?= $this->Paginator->sort('disciplina', __('Disciplina')) ?></th>
                    </thead>
                    <tbody>
                        <?php foreach ($conflictedAndUnallocatedClazzes as $subjectId => $subjectInfo): ?>
                            <tr>
                                <td> <?= $this->Number->format($subjectId) ?> </td>
                                <td> <?= h($subjectInfo['subjectName']) ?></td>
                            </tr>
                        <?php endforeach ?>
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
    <div class="col-xs-6">
        <div class="box box-solid">
            <div class="box-header">
                <h3 class="box-title">Docentes sub/super alocados</h3>
            </div>
            <div class="box-body table-responsive no-padding">
                <table class="table table-striped table-valign-middle">
                    <thead>
                        <tr>
                            <th><?= $this->Paginator->sort('matricula', __('Matrícula')) ?></th>
                            <th><?= $this->Paginator->sort('docente', __('Docente')) ?></th>
                            <th><?= $this->Paginator->sort('status', __('Status')) ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($subAndSuperAllocatedTeachers as $teacherId => $teacherInfo): ?>
                            <tr>
                                <td> <?= h($teacherInfo['registry']) ?> </td>
                                <td> <?= h($teacherInfo['userName']) ?></td>
                                <td> <?= h($teacherId) ?></td>
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

<!-- BUTTON -->
<div class="row">
	<div class="col-xs-12 text-center">
		<?= $this->Html->link(
            '<i class="fa fa-magic"></i> ' . __('Simular Distribuição Automática'),
            ['action' => 'simulate'],
            [
                'escape' => false,
                'class' => 'btn btn-sm btn-primary'
            ]
        );
        ?>
	</div>
</div>
