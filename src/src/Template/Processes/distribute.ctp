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
                        <?php foreach ($clazzes as $clazz): ?>
                            <tr>
                                <td><?= $this->Number->format($clazz->subject->id) ?></td>
                                <td><?= h($clazz->subject->name) ?></td>
                                <td>
                                	<?php foreach($clazz->effectiveTeachers as $teacher): ?>
                                		<?= h($teacher->registry) ?><br>
                                	<?php endforeach; ?>
                                </td>
								<td><?= $clazz->displayEffectiveTeachers ?></td>
								<td>
									<?php foreach($clazz->locals as $local): ?>
                                		<?php echo ($local->name .' ['. $local->address .']') ?><br>
                                	<?php endforeach; ?>
								</td>
								<td>
									<?php foreach($clazz->locals as $local): ?>
                                		<?php echo ($local->_joinData->schedule_id) ?><br>
                                	<?php endforeach; ?>
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

<!-- SECOND AND THIRD TABLES -->
<div class="row">
    <div class="col-xs-6">
        <div class="box box-solid">
            <div class="box-header">
                <h3 class="box-title">Disciplinas ainda não alocadas ou conflitantes</h3>
            </div>
            <div class="box-body table-responsive no-padding">
                <table class="table table-striped table-valign-middle">
                    <thead>
                        <tr>
                            <th><?= $this->Paginator->sort('codDisciplina', __('#CodDisciplina')) ?></th>
                            <th><?= $this->Paginator->sort('disciplina', __('Disciplina')) ?></th>
                    </thead>
                    <tbody>
                        <tr>
                            <td>INF0123</td>
                            <td>Prática em Engenharia de Software</td>
                        </tr>
                        <tr>
                            <td>INF1245</td>
                            <td>Métodos Avançados</td>
                        </tr>
                        <tr>
                            <td>INF0436</td>
                            <td>Dispositivos Móveis</td>
                        </tr>
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
                <h3 class="box-title">Docentes sub/super alocados ou conflitantes</h3>
            </div>
            <div class="box-body table-responsive no-padding">
                <table class="table table-striped table-valign-middle">
                    <thead>
                        <tr>
                            <th><?= $this->Paginator->sort('matricula', __('Matrícula')) ?></th>
                            <th><?= $this->Paginator->sort('docente', __('Docente')) ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>20122287</td>
                            <td>Anderson Moreira</td>
                        </tr>
                        <tr>
                            <td>20113487</td>
                            <td>Eugênio Simon</td>
                        </tr>
                        <tr>
                            <td>20018752</td>
                            <td>Mariela Euclides da Cunha</td>
                        </tr>
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