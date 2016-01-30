<?php $this->assign('title', 'Distribuição Automática'); ?>
<?php $this->start('breadcrumb'); ?>
    <li><a href="#"><i class="fa fa-dashboard"></i> Nível 1</a></li>
    <li class="active">Nível 2</li>
<?php $this->end(); ?>

<div class="processes index large-9 medium-8 columns content">
    <h3><?= __('Reverter distribuição automática') ?></h3>
	<div id="wrapper" style="margin-top:20px;margin-bottom:20px;display:flex;">
		<div style="width:50%;flex: 0 0 50%;">
			<h4>Processos de distribuição "em aberto"</h4>
			<div>
				<div class="radio">
					<label><input type="radio" name="optradio">2015/1</label>
				</div>
				<div class="radio">
					<label><input type="radio" name="optradio">2015/2</label>
				</div>
				<div class="radio">
					<label><input type="radio" name="optradio">2016/1</label>
				</div>
			</div>
			<div class="text-center">
				<button type="button" class="btn btn-danger"  data-toggle="modal" data-target="#reversao-modal">REVERTER DISTRIBUIÇÃO</button>
			</div>
		</div>
		<div style="width:50%;flex:1%">
			<div>
				<p>
					O Processo de distribuição será revertido ao estágio anterior ao da efetivação 
					da distribuição automática, ou seja, quaisquer disciplinas que não sofreram a 
					distribuição automática (já possuíam docentes alocados e não eram conflitantes) 
					serão mantidas.
				</p>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" tabindex="-1" role="dialog" id="reversao-modal" aria-labelledby="mySmallModalLabel">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Distribuição Automática</h4>
			</div>
			<div class="modal-body">
				<p><i class="glyphicon glyphicon-ok"></i> Distribuição automática 2015/2 revertida com sucesso!</p>
			</div>
		</div>
	</div>
</div>