<div class="navbar-custom-menu">
    <ul class="nav navbar-nav">
        <!-- Notifications Menu -->
        <li class="dropdown notifications-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                <i class="fa fa-bell-o"></i>
                <?php if(!empty($loggedUser->latest_notifications)): ?>
                    <span class="label label-warning"><?= count($loggedUser->latest_notifications) ?></span>
                <?php endif; ?>
            </a>
            <ul class="dropdown-menu">
                <li class="header"><?= __(sprintf('Você possui %d notificações', count($loggedUser->latest_notifications))) ?></li>
                <li>
                    <ul class="menu">
                        <?php if(count($loggedUser->latest_notifications) < 1): ?>
                        <li>
                            <a href="#">
                                <i class="fa fa-circle-o text-yellow"></i> <?= __('Nenhuma notificação pendente') ?>
                            </a>
                        </li>
                        <?php else: ?>
                        <?php foreach($loggedUser->latest_notifications as $notification): ?>
                        <li>
                            <a href="#">
                                <i class="fa fa-circle-o text-blue"></i> <?= $notification->description ?>
                            </a>
                        </li>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                </li>
                <li class="footer"><a href="#"><?= __('Visualizar todas') ?></a></li>
            </ul>
        </li>

        <!-- User Account Menu -->
        <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                <?= $this->Gravatar->generate(
                    $loggedUser->email,
                    [
                        'image-options' => ['class' => 'user-image'],
                        'size' => 160,
                        'default' => 'mm'
                    ]
                ) ?>
                <span class="hidden-xs"><?= $loggedUser->name ?></span>
            </a>
            <ul class="dropdown-menu">
                <li class="user-header">
                    <?= $this->Gravatar->generate(
                        $loggedUser->email,
                        [
                            'image-options' => ['class' => 'img-circle'],
                            'size' => 160,
                            'default' => 'mm'
                        ]
                    ) ?>
                    <p>
                        <?= $loggedUser->name ?>
                        <small>Administrador</small>
                    </p>
                </li>
                <li class="user-footer">
                    <div class="pull-left">
                        <?= $this->Html->link(__('Minha conta'), ['controller' => 'users', 'action' => 'myAccount'], ['class' => 'btn btn-default btn-flat']) ?>
                    </div>
                    <div class="pull-right">
                        <?= $this->Html->link(__('Sair'), ['controller' => 'users', 'action' => 'logout'], ['class' => 'btn btn-default btn-flat']) ?>
                    </div>
                </li>
            </ul>
        </li>
    </ul>
</div>
