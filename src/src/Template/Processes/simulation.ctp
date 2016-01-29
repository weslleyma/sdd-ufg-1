<?php $this->assign('title', 'Distribuição Automática'); ?>
<?php $this->start('breadcrumb'); ?>
    <li><a href="#"><i class="fa fa-dashboard"></i> Nível 1</a></li>
    <li class="active">Nível 2</li>
<?php $this->end(); ?>

<div class="processes index large-9 medium-8 columns content">
    <h3><?= __('Simular distribuição automática') ?></h3>
    <div style="margin-top:20px;">
		<h4>Alocados manualmente (não sofreram distribuição automática) - Já possuiam docente alocado e não apresentavam conflito</h4>
		<div style="height:150px; overflow:auto;">
			<table class="table table-hover simple-table">
				<thead>
					<tr>
						<td>DISCIPLINA</td>
						<td>DOCENTE</td>
						<td>LOCAL</td>
						<td>TURMA</td>
						<td>HORÁRIO</td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>[INF001ES003] Mercado de Software</td>
						<td>20121111 José</td>
						<td>INF309</td>
						<td>B</td>
						<td>5N1234</td>
					</tr>
					<tr>
						<td>[INF001ES003] Mercado de Software</td>
						<td>20121111 José</td>
						<td>INF309</td>
						<td>B</td>
						<td>5N1234</td>
					</tr>
					<tr>
						<td>[INF001ES003] Mercado de Software</td>
						<td>20121111 José</td>
						<td>INF309</td>
						<td>B</td>
						<td>5N1234</td>
					</tr>
					<tr>
						<td>[INF001ES003] Mercado de Software</td>
						<td>20121111 José</td>
						<td>INF309</td>
						<td>B</td>
						<td>5N1234</td>
					</tr>
				</tbody>
			</table>
		</div>
    </div>
	<div style="margin-top:20px">
		<h4>Alocados dinamicamente (sofreram distribuição automática) - Disciplinas sem docentes ou conflitantes ou sub/super alocados</h4>
		<div style="height:150px; overflow:auto;">
			<table class="table table-hover simple-table">
				<thead>
					<tr>
						<td>DISCIPLINA</td>
						<td>DOCENTE</td>
						<td>LOCAL</td>
						<td>TURMA</td>
						<td>HORÁRIO</td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>[INF001ES003] Mercado de Software</td>
						<td>20121111 José</td>
						<td>INF309</td>
						<td>B</td>
						<td>5N1234</td>
					</tr>
					<tr>
						<td>[INF001ES003] Mercado de Software</td>
						<td>20121111 José</td>
						<td>INF309</td>
						<td>B</td>
						<td>5N1234</td>
					</tr>
					<tr>
						<td>[INF001ES003] Mercado de Software</td>
						<td>20121111 José</td>
						<td>INF309</td>
						<td>B</td>
						<td>5N1234</td>
					</tr>
					<tr>
						<td>[INF001ES003] Mercado de Software</td>
						<td>20121111 José</td>
						<td>INF309</td>
						<td>B</td>
						<td>5N1234</td>
					</tr>
				</tbody>
			</table>
		</div>
    </div>
	<div class="text-center" style="margin-top:20px;">
		<button type="button" class="btn btn-danger">Cancelar</button>
		<button type="button" class="btn btn-success" data-toggle="modal" data-target="#efetivamento-modal">Efetivar Distribuição</button>
	</div>
</div>
<div class="modal fade" tabindex="-1" role="dialog" id="efetivamento-modal" aria-labelledby="mySmallModalLabel">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Distribuição Automática</h4>
			</div>
			<div class="modal-body">
				<p><i class="glyphicon glyphicon-ok"></i> Distribuição efetivada com sucesso!</p>
			</div>
		</div>
	</div>
</div>