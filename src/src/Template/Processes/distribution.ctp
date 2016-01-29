<?php $this->assign('title', 'Distribuição Automática'); ?>
<?php $this->start('breadcrumb'); ?>
    <li><a href="#"><i class="fa fa-dashboard"></i> Nível 1</a></li>
    <li class="active">Nível 2</li>
<?php $this->end(); ?>

<div class="processes index large-9 medium-8 columns content">
    <h3><?= __('Simular distribuição automática') ?></h3>
    <div style="margin-top:20px;">
		<h4>Disciplinas com docentes já alocados e não conflitantes:</h4>
		<div style="height:150px; overflow:auto;">
			<table class="table table-hover">
				<tbody>
					<tr>
						<td>[INF001ES003] Mercado de Software - [11111111 José]</td>
					</tr>
					<tr>
						<td>[INF001ES003] Mercado de Software - [11111111 José]</td>
					</tr>
					<tr>
						<td>[INF001ES003] Mercado de Software - [11111111 José]</td>
					</tr>
					<tr>
						<td>[INF001ES003] Mercado de Software - [11111111 José]</td>
					</tr>
					<tr>
						<td>[INF001ES003] Mercado de Software - [11111111 José]</td>
					</tr>
					<tr>
						<td>[INF001ES003] Mercado de Software - [11111111 José]</td>
					</tr>
				</tbody>
			</table>
		</div>
    </div>
    <div id="wrapper" style="margin-top:20px;margin-bottom:20px;display:flex;">
		<div style="width:50%;flex: 0 0 50%;">
			<h4>Disciplinas com docentes ainda não alocados ou conflitantes:</h4>
			<div style="height:150px; overflow:auto;">
				<table class="table table-hover">
					<tbody>
						<tr>
							<td>[INF001ES004] Práticca em Engenharia de Software</td>
						</tr>
						<tr>
							<td>[INF001ES004] Práticca em Engenharia de Software</td>
						</tr>
						<tr>
							<td>[INF001ES004] Práticca em Engenharia de Software</td>
						</tr>
						<tr>
							<td>[INF001ES004] Práticca em Engenharia de Software</td>
						</tr>
						<tr>
							<td>[INF001ES004] Práticca em Engenharia de Software</td>
						</tr>
						<tr>
							<td>[INF001ES004] Práticca em Engenharia de Software</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
		<div style="width:50%;flex:1%;">
			<h4>Disciplinas com horas ainda não alocados ou conflitantes:</h4>
			<div style="height:150px; overflow:auto;">
				<table class="table table-hover">
					<tbody>
						<tr>
							<td>2222222 João</td>
						</tr>
						<tr>
							<td>2222222 João</td>
						</tr>
						<tr>
							<td>2222222 João</td>
						</tr>
						<tr>
							<td>2222222 João</td>
						</tr>
						<tr>
							<td>2222222 João</td>
						</tr>
						<tr>
							<td>2222222 João</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<div class="text-center">
		<button type="button" class="btn btn-primary">Simular Distribuição</button>
	</div>
</div>