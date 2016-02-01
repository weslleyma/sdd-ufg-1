<?php $this->assign('title', 'Docentes'); ?>
<?php $this->start('breadcrumb'); ?>
<li><?= $this->Html->link('<i class="fa fa-dashboard"></i>' . __('Dashboard'), '/', ['escape' => false]) ?></li>
<li><?= $this->Html->link(__('Docentes'), ['action' => 'index']) ?></li>
<li class="active">Alocar Turmas para #<?= $teacher->id ?></li>
<?php $this->end(); ?>

<?php 
	use Cake\Routing\Router;
	$this->loadHelper('Utils'); 
?>

<div id="message" role="alert" class="alert alert-dismissible fade in alert-success" style="display: none;">
	<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>.
</div>

<?= $this->Form->create('Filtros', array('action' => 'allocateClazzes/' . $teacher->id)) ?>

<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Alocar Turmas para docente #<?= $teacher->name ?></h3>
				<div class="pull-right box-tools">
					<?= $this->Html->link(
						'',
						['action' => 'edit', $teacher->id],
						[
							'title' => __('Ir para informações do docente'),
							'class' => 'btn btn-sm btn-default glyphicon glyphicon-education',
							'data-toggle' => 'tooltip',
							'data-original-title' => __('Cadastro do Docente'),
						]
					) ?>
				</div>
            </div>
            <div class="box-body">
				<?php if (count($processes) >= 1) { ?>
				<div class="row">
					<div class="col-xs-12" id="filtros">
						<fieldset>
							<legend>Filtros</legend>
							<div class="row">
								<div class="col-xs-3">
									<?php 
										echo $this->Form->input('course_name', ['label' => 'Nome do Curso', 'placeholder' => 'Nome do Curso', 'class' => 'col-xs-3']);
									?>
								</div>
								<div class="col-xs-3">
									<?php
										echo $this->Form->input('knowledge_name', ['label' => 'Nome do Núcleo', 'placeholder' => 'Nome do Núcleo', 'class' => 'col-xs-3']);
									?>
								</div>
								<div class="col-xs-3">
									<?php
										echo $this->Form->input('subject_name', ['label' => 'Nome da Disciplina', 'placeholder' => 'Nome da Disciplina', 'class' => 'col-xs-3']);
									?>
								</div>
								<div class="col-xs-3">
									<?php
										echo $this->Form->input('clazz_name', ['label' => 'Nome da Turma', 'placeholder' => 'Nome da Turma', 'class' => 'col-xs-3']);
									?>
								</div>
								<div class="col-xs-3">
									<?php
										echo $this->Form->input('week_day', ['label' => 'Dia da Semana', 'placeholder' => 'Dia da Semana', 'class' => 'col-xs-3']);
									?>
								</div>
								<div class="col-xs-3">
									<?php
										echo $this->Form->input('start_time', ['label' => 'Horário de Início', 'placeholder' => 'Horário de Início', 'class' => 'col-xs-3']);
									?>
								</div>
								<div class="col-xs-3">
									<?php
										echo $this->Form->input('end_time', ['label' => 'Horário de Término', 'placeholder' => 'Horário de Término', 'class' => 'col-xs-3']);
									?>
								</div>
								<div class="col-xs-3">
									<?= $this->Html->link(
										'',
										['controller' => 'Teachers', 'action' => 'filterClazzes'],
										[
											'title' => __('Filtrar'),
											'class' => 'btn btn-lg btn-success glyphicon glyphicon-search',
											'data-toggle' => 'tooltip',
											'data-original-title' => __('Filtrar')
										]
									) ?>
								</div>
							</div>
						</fieldset>
					</div>
				</div>
				<br>
				<div class="row">
					<div class="col-xs-12">
						<fieldset>
							<legend>Turmas em Aberto</legend>
							<table class="table table-striped table-valign-middle">
								<thead>
								<tr>												
									<th><?= $this->Paginator->sort('clazz_id',__('#ID')) ?></th>
									<th><?= $this->Paginator->sort('clazze.subject.name',__('Disciplina')) ?></th>
									<th><?= $this->Paginator->sort('schedule.week_day', __('Dia da Semana')) ?></th>
									<th><?= $this->Paginator->sort('schedule.start_time', __('Horário Início')) ?></th>
									<th><?= $this->Paginator->sort('schedule.end_time', __('Horário Término')) ?></th>
									<th><?= $this->Paginator->sort('local.address', __('Local')) ?></th>
									<th><?= $this->Paginator->sort('clazze.subject.knowledge.name', __('Núcleo')) ?></th>
									<th><?= $this->Paginator->sort('clazze.subject.course.name', __('Curso')) ?></th>
									<th><?= $this->Paginator->sort('name', __('Name')) ?></th>
									<th width="200px"><?= __('Ações') ?></th>
								</tr>
								</thead>
								<tbody>
								<?php if(count($clazzes) < 1): ?>
									<tr>
										<td colspan="10" class="text-center">Ainda não existem turmas cadastradas no Processo</td>
									</tr>
								<?php endif; ?>
								<?php foreach ($clazzes as $clazz): ?>
									<tr>
										<td><?= h($clazz->id) ?></td>
										<td><?= h($clazz->subject->name) ?></td>
										<td><?= h($clazz->_matchingData['Schedules']->week_day) ?></td>
										<td><?= h($clazz->_matchingData['Schedules']->start_time) ?></td>
										<td><?= h($clazz->_matchingData['Schedules']->end_time) ?></td>
										<td><?= h($clazz->_matchingData['Locals']->address) ?></td>
										<td><?= h($clazz->subject->knowledge->name) ?></td>
										<td><?= h($clazz->subject->course->name) ?></td>
										<td><?= h($clazz->name) ?></td>
										<td>
											<?= $this->Html->link(
												'',
												['controller' => 'Clazzes', 'action' => 'view', $clazz->clazz_id],
												[
													'title' => __('Visualizar'),
													'class' => 'btn btn-sm btn-default glyphicon glyphicon-search',
													'data-toggle' => 'tooltip',
													'data-original-title' => __('Visualizar'),
												]
											) ?>
											<?php 	$has_clazz = false;
													foreach ($teacher->clazzes as $c) : 
														if ($clazz->id == $c->id) { ?>
											
														<?= $this->Form->button('<i id="icon-' . $clazz->id . '" class="fa fa-remove"></i>'
															, array(
																'type' => 'button',
																'id' => 'button-' . $clazz->id,
																'class' => 'btn btn-sm btn-danger',
																'data-toggle' => 'tooltip',
																'title' => 'Cancelar inscricao na disciplina',
																'onclick' => 'allocateClazz(' . $teacher->id . ', ' . $clazz->id . ', ' . 'false' . ')',
																)
														) ?>
														
														<?php 
																$has_clazz = true;
																break;
															} ?>
														<?php endforeach; ?>
														<?php
														if (!$has_clazz) {
														?>
															<?= $this->Form->button('<i id="icon-' . $clazz->id . '" class="fa fa-check"></i>'
															, array(
																'type' => 'button',
																'id' => 'button-' . $clazz->id,
																'class' => 'btn btn-sm btn-success',
																'data-toggle' => 'tooltip',
																'title' => 'Inscrever-se na disciplina',
																'onclick' => 'allocateClazz(' . $teacher->id . ', ' . $clazz->id . ', ' . 'true' . ')',
																)
														) ?>
															
														<?php
														}
											?>
											<div id="situation">
												<?php
													if ($has_clazz) {
														echo 'Inscrito';
													} else {
														echo 'Não Inscrito';
													}
												?>
											</div>
										</td>
									</tr>
								<?php endforeach; ?>
								</tbody>
							</table>
						</fieldset>
					</div>
				</div>
				<?php } else { ?>
					Não existem Processos de Distribuição em Aberto.
				<?php } ?>
			</div>
            <div class="box-footer clearfix">
                <?= $this->Form->button(__('Salvar'), ['class' => 'btn btn-success']) ?>
            </div>
        </div>
    </div> 
</div>
<?= $this->Form->end() ?>
<script>
function allocateClazz(teacher, clazz, allocate) {
	$.ajax({
		type:"GET",
		url:"<?php echo Router::url(array('controller'=>'Teachers','action'=>'allocateClazzes'));?>/"+teacher+"/"+clazz+"/"+allocate,
		dataType: 'html',
		async:false,
		success: function(tab){
			if (tab == 'success') {
				$('#message').empty();
				$('#situation').empty();
				if (allocate) {
					$('#message').removeClass('alert-warning').removeClass('alert-error');
					$('#message').addClass('alert-success');
					$('#message').append('Interesse na disciplina registrado com sucesso!');
					$('#button-' + clazz).removeClass('btn-success').addClass('btn-danger');
					$('#button-' + clazz).attr('onclick', 'allocateClazz(' + teacher + ', ' + clazz + ', ' + 'false' + ')');
					$('#button-' + clazz).attr('title', 'Cancelar inscricao na disciplina');
					$('#button-' + clazz).attr('data-original-title', 'Cancelar inscricao na disciplina');
					$('#icon-' + clazz).removeClass('fa-check').addClass('fa-remove');
					$('#situation').append('Inscrito');
				} else {
					$('#message').removeClass('alert-success').removeClass('alert-error');
					$('#message').addClass('alert-warning');
					$('#message').append('Interesse na disciplina cancelado com sucesso!');
					$('#button-' + clazz).removeClass('btn-danger').addClass('btn-success');
					$('#button-' + clazz).attr('onclick', 'allocateClazz(' + teacher + ', ' + clazz + ', ' + 'true' + ')');
					$('#button-' + clazz).attr('title', 'Inscrever-se na disciplina');
					$('#button-' + clazz).attr('data-original-title', 'Inscrever-se na disciplina');
					$('#icon-' + clazz).removeClass('fa-remove').addClass('fa-check');
					$('#situation').append('Não Inscrito');
				}
	
			} else {

				$('#message').removeClass('warning').removeClass('success');
				$('#message').addClass('error');
				$('#message').html('Ocorreu um erro ao tentar efetuar a operação. Tente novamente ou contate o administrador do sistema.');
			}
			
			$('#message').toggle();
		},
		error: function (tab) {
			alert('error');
		}
	});
}
</script>
