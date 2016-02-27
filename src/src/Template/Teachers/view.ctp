<?php $this->assign('title', 'Docente: '.$teacher->displayField); ?>
<?php $this->start('breadcrumb'); ?>
    <li><?= $this->Html->link('<i class="fa fa-dashboard"></i>' . __('Dashboard'), '/', ['escape' => false]) ?></li>
    <li><?= $this->Html->link(__('Docentes'), ['action' => 'index']) ?></li>
    <li class="active"><?= $teacher->displayField ?></li>
<?php $this->end(); ?>

<div class="row">
    <div class="col-sm-5 col-md-4 col-lg-3">
        <div class="box box-primary">
            <div class="box-body box-profile">
                <div class="profile-images-multiple">
                    <?php if(!empty($clazz->selectedTeachers)): ?>
                        <?php
                            echo $this->Html->link(
                                $this->Gravatar->generate(
                                    $teacher->user->email,
                                    [
                                        'image-options' => ['class' => 'profile-user-img img-responsive img-circle'],
                                        'size' => 160,
                                        'default' => 'mm'
                                    ]
                                ),
                                ['controller' => 'Teachers', 'action' => 'view', $teacher->id],
                                ['escape' => false]
                            );
                        ?>
                    <?php else: ?>
                        <?= $this->Html->image('no-chosen-clazz.png', [
                            'class' => 'profile-user-img img-responsive img-circle'
                        ]) ?>
                    <?php endif; ?>
                </div>

                <div class="profile-username-box">
                    <a class="profile-username"><?= $teacher->displayField ?></a>
                </div>

                <div class="table-responsive no-padding">
                    <table class="table-profile">
                        <tr>
                            <td width="170px"><b><?= __('ID') ?></b></td></td>
                            <td><?= $this->Number->format($teacher->id) ?></td>
                        </tr>
                        <?php if(isset($loggedUser->teacher) && $loggedUser->teacher->id == $teacher->id): ?>
                            <tr>
                                <td><b><?= __('Login') ?></b></td>

                                <td><?= $this->Html->link(
                                    $teacher->user->login,
                                    ['controller' => 'Users','action' => 'edit', $teacher->user->id]) ?></td>
                            </tr>
                        <?php endif; ?>
                        <tr>
                            <th><?= __('Email') ?></th>
                            <td><?= h($teacher->user->email) ?></td>
                        </tr>
                        <tr>
                            <th><?= __('Administrador') ?></th>
                            <td><?= h($teacher->user->is_admin ? 'Sim' : 'Não') ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-7 col-md-8 col-lg-9">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#general" data-toggle="tab">Informações gerais</a></li>
                <li><a href="#roles" data-toggle="tab">Papéis</a></li>
                <li><a href="#knowledges" data-toggle="tab">Núcleos de Conhecimento</a></li>
                <li><a href="#clazzes" data-toggle="tab">Turmas</a></li>
                <li><a href="#schedules" data-toggle="tab">Locais/Horários de aula</a></li>
            </ul>
            <div class="tab-content no-padding">
                <div class="tab-pane table-responsive active" id="general">
                    <div class="box-body table-responsive no-padding">
                        <div class="box-body table-responsive no-padding">
                            <table class="table table-striped table-valign-middle">
                                <tr>
                                    <th><?= __('Formação') ?></th>
                                    <td><?= h($teacher->formation) ?></td>
                                </tr>
                                <tr>
                                    <td><b><?= __('Registro') ?></b></td>
                                    <td><?= h($teacher->registry) ?></td>
                                </tr>
                                <tr>
                                    <th><?= __('Link do Lattes') ?></th>
                                    <td><?= h($teacher->url_lattes) ?></td>
                                </tr>
                                <tr>
                                <th><?= __('RG') ?></th>
                                    <td><?= h($teacher->rg) ?></td>
                                </tr>
                                <tr>
                                    <th><?= __('CPF') ?></th>
                                    <td><?= h($teacher->cpf) ?></td>
                                </tr>
                                <tr>
                                    <th><?= __('Carga Horária') ?></th>
                                    <td><?= $this->Number->format($teacher->workload) ?></td>
                                </tr>
                                <tr>
                                    <th><?= __('Data de Ingresso') ?></th>
                                    <td><?= h($teacher->entry_date ?
                                        $teacher->entry_date->i18nFormat('dd/MM/yyyy') : '') ?></td>
                                </tr>
                                <tr>
                                    <th><?= __('Data de Nascimento') ?></th>
                                    <td><?= h($teacher->birth_date ?
                                        $teacher->birth_date->i18nFormat('dd/MM/yyyy') : '') ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="tab-pane" id="roles">
                    <table class="table table-striped table-valign-middle">
                        <thead>
                            <tr>
                                <th><?= $this->Paginator->sort('id', __('#ID')) ?></th>
                                <th><?= $this->Paginator->sort('type', __('Tipo')) ?></th>
                                <th><?= $this->Paginator->sort('type', __('Núcleo de Conhecimento')) ?></th>
                                <th width="200px"><?= __('Ações') ?></th>
                            </tr>
                        </thead>
                        <tbody>

                        <?php if(count($teacher->roles) < 1): ?>
                            <tr>
                                <td colspan="3" class="text-center">Esse docente não possui nenhum papel associado</td>
                            </tr>
                        <?php endif; ?>

                        <?php foreach ($teacher->roles as $role): ?>
                            <tr>
                                <td><?= $this->Number->format($role->id) ?></td>
                                <td><?= h($role->type == 'COORDINATOR'
                                    ? 'Coordenador' : 'Facilitador') ?></td>
                                <td><?= h($role->type == 'FACILITATOR'
                                    ? $role->knowledge->name : '') ?></td>
                                <td>
                                    <?= $this->Html->link(
                                        '',
                                        ['action' => 'view', $role->id],
                                        [
                                            'title' => __('Visualizar'),
                                            'class' => 'btn btn-sm btn-default glyphicon glyphicon-search',
                                            'data-toggle' => 'tooltip',
                                            'data-original-title' => __('Visualizar'),
                                        ]
                                    ) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>



                <div class="tab-pane" id="knowledges">
                    <table class="table table-striped table-valign-middle">
                        <thead>
                            <tr>
                                <th><?= $this->Paginator->sort('id', __('#ID')) ?></th>
                                <th><?= $this->Paginator->sort('name', __('Nome')) ?></th>
                                <th><?= $this->Paginator->sort('name', __('Level')) ?></th>
                                <th width="200px"><?= __('Ações') ?></th>
                            </tr>
                        </thead>
                        <tbody>

                        <?php if(count($teacher->knowledges_teachers) < 1): ?>
                            <tr>
                                <td colspan="3" class="text-center">Esse docente não possui nenhum núcleo de conhecimento associado</td>
                            </tr>
                        <?php endif; ?>

                        <?php foreach ($teacher->knowledges_teachers as $knowledgeTeacher): ?>
                            <tr>
                                <td><?= $this->Number->format($knowledgeTeacher->knowledge->id) ?></td>
                                <td><?= h($knowledgeTeacher->knowledge->name) ?></td>
                                <td><?= h($knowledgeTeacher->level) ?></td>
                                <td>
                                    <?= $this->Html->link(
                                        '',
                                        ['action' => 'view', $knowledgeTeacher->knowledge->id],
                                        [
                                            'title' => __('Visualizar'),
                                            'class' => 'btn btn-sm btn-default glyphicon glyphicon-search',
                                            'data-toggle' => 'tooltip',
                                            'data-original-title' => __('Visualizar'),
                                        ]
                                    ) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>



                <div class="tab-pane" id="clazzes">
                    <table class="table table-striped table-valign-middle">
                        <thead>
                            <tr>
                                <th><?= $this->Paginator->sort('id', __('#ID')) ?></th>
                                <th><?= $this->Paginator->sort('name', __('Nome')) ?></th>
                                <th><?= $this->Paginator->sort('vacancies', __('Vagas')) ?></th>
                                <th><?= $this->Paginator->sort('formation', __('Local/Hora')) ?></th>
                                <th><?= $this->Paginator->sort('process', __('Processo de distribuição')) ?></th>
                                <th width="200px"><?= __('Ações') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(count($teacher->clazzes) < 1): ?>
                                <tr>
                                    <td colspan="6" class="text-center">Esse docente não possui nenhuma turma associada</td>
                                </tr>
                            <?php endif; ?>

                            <?php foreach ($teacher->clazzes as $clazze): ?>
                                <tr>
                                    <td><?= $this->Number->format($clazze->id) ?></td>
                                    <td><?= h($clazze->name) ?></td>
                                    <td><?= h($clazze->vacancies) ?></td>
                                    <td><?= h($clazze->local) ?></td>
                                    <td><?= h($clazze->process->name) ?></td>
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
                                            ['controller' => 'Clazzes','action' => 'edit', $clazze->id],
                                            [
                                                'title' => __('Editar'),
                                                'class' => 'btn btn-sm btn-primary glyphicon glyphicon-pencil',
                                                'data-toggle' => 'tooltip',
                                                'data-original-title' => __('Editar'),
                                            ]
                                        ) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="tab-pane" id="schedules" style="padding: 10px;">
                    <div class="content week-box" style="padding-bottom: 0;">
                        <?php
                        $index = 0;
                        foreach($this->Utils->daysOfWeek() as $week_day => $name):
                            if(!is_numeric($week_day) || $week_day < 2) {
                                continue;
                            }

                            if(($index % 3) == 0) {
                                if($index != 0) {
                                    echo '</div>';
                                }
                                echo '<div class="row">';
                            }
                            $index++;
                            ?>
                            <div class="col-sm-4 week-day-box">
                                <div class="week-title"><?= h($name) ?></div>
                                <div class="week-day" data-week-day="<?= $week_day ?>">
                                    <?php foreach ($scheduleLocals as $scheduledLocal): ?>
                                        <?php if($scheduledLocal->week_day == $week_day): ?>
                                            <div class="label label-primary schedule-data-div">
                                            <span class="text-ellipsis" style="max-width: calc(100% - 15px);">
                                                <?= $scheduledLocal->schedule->period ?>: <?= $scheduledLocal->local->fullPath ?>
                                            </span>
                                            </div>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
