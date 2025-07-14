<?php
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;

/* @var $this \yii\web\View */
/* @var $content string */

$this->beginPage()
?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>

    <!-- AdminLTE -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/css/adminlte.min.css " />
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css " />
</head>
<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed">
<div class="wrapper">

    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-dark navbar-info">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
        </ul>
    </nav>

    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <!-- Brand Logo -->
        <a href="/" class="brand-link">
            <span class="brand-text font-weight-light">m4leads</span>
        </a>

        <!-- Sidebar -->
        <div class="sidebar">
            <!-- Sidebar user panel (optional) -->
            <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                <div class="info">
                    <a href="#" class="d-block"><?= Yii::$app->user->identity->username ?? 'Администратор' ?></a>
                </div>
            </div>

            <!-- Sidebar Menu -->
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

                    <!-- Главная -->
                    <li class="nav-item">
                        <a href="/" class="nav-link">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Главная</p>
                        </a>
                    </li>

                    <li class="nav-header">Модули</li>

                    <!-- Заказы -->
                    <li class="nav-item">
                        <a href="/orders" class="nav-link">
                            <i class="nav-icon fas fa-shopping-cart"></i>
                            <p>Заказы</p>
                        </a>
                    </li>

                    <!-- Статусы 
                    <li class="nav-item">
                        <a href="/admin/status/index" class="nav-link">
                            <i class="nav-icon fas fa-check-circle"></i>
                            <p>Статусы</p>
                        </a>
                    </li> -->

                    <li class="nav-header">Настройки</li>

                    
                    <!-- Страны -->
                    <li class="nav-item">
                        <a href="/countries" class="nav-link">
                            <i class="nav-icon fas fa-flag"></i>
                            <p>Страны</p>
                        </a>
                    </li>

                    <!-- Офферы -->
                    <li class="nav-item">
                        <a href="/offers" class="nav-link">
                            <i class="nav-icon fas fa-bullhorn"></i>
                            <p>Офферы</p>
                        </a>
                    </li>

                    <!-- Партнёры -->
                    <li class="nav-item">
                        <a href="/webs" class="nav-link">
                            <i class="nav-icon fas fa-user-friends"></i>
                            <p>Вебы</p>
                        </a>
                    </li>

                    <!-- Пользователи 
                    <li class="nav-item">
                        <a href="/admin/user/index" class="nav-link">
                            <i class="nav-icon fas fa-users-cog"></i>
                            <p>Пользователи</p>
                        </a>
                    </li> -->

                    <!-- Логи -->
                    <li class="nav-item">
                        <a href="/admin/log/index" class="nav-link">
                            <i class="nav-icon fas fa-file-alt"></i>
                            <p>Логи</p>
                        </a>
                    </li>

                </ul>
            </nav>
            <!-- /.sidebar-menu -->
        </div>
        <!-- /.sidebar -->
    </aside>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0"><?= Html::encode($this->title) ?></h1>
                    </div>
                    <div class="col-sm-6">
                        <?= Breadcrumbs::widget([
                            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                        ]) ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <?= $content ?>
            </div>
        </section>
    </div>

    <!-- Main Footer -->
    <footer class="main-footer">
        <strong>© m4leads.org • <?= date('Y') ?></strong>
    </footer>
</div>

<?php $this->endBody() ?>

<!-- AdminLTE App -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/js/adminlte.min.js "></script>
</body>
</html>
<?php $this->endPage() ?>