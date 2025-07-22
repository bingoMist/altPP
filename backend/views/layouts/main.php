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
    <script src="/assets/35df895c/jquery.js"></script>
    <!-- AdminLTE -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/css/adminlte.min.css " />
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css " />
    <style>
.user-menu .dropdown-menu {
    min-width: 200px;
    padding: 10px;
    border-radius: 0.35rem;
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
    border: none;
    background-color: #f6f9fc;
    font-size: 14px;
}

.user-menu .dropdown-item {
    padding: 10px 15px;
    border-radius: 0.25rem;
    transition: background-color 0.3s;
}

.user-menu .dropdown-item:hover {
    background-color: #e9ecef;
}

.user-menu .dropdown-divider {
    margin: 10px 0;
    border-top: 1px solid #dee2e6;
}

.user-menu .dropdown-footer {
    padding-top: 5px;
}

.user-menu .btn-flat {
    font-size: 13px;
    padding: 6px 10px;
    background-color: #007bff;
    color: white;
    width: 100%;
    border-radius: 0.25rem;
}

.user-menu .btn-flat:hover {
    background-color: #0056b3;
}
</style>
</head>
<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed">
<div class="wrapper">

<nav class="main-header navbar navbar-expand navbar-dark navbar-info">
    <!-- Левая часть -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                <i class="fas fa-bars"></i>
            </a>
        </li>
    </ul>

    <!-- Правая часть -->
    <ul class="navbar-nav ml-auto">
        <li class="nav-item dropdown user-menu">
            <a href="#" class="nav-link dropdown-toggle" id="userDropdown" role="button">
                <span class="nav-icon fas fa-user"></span>
                <span class="d-none d-md-inline"><?= Yii::$app->user->isGuest ? 'Гость' : Yii::$app->user->identity->username ?></span>
            </a>
            <div class="dropdown-menu dropdown-menu-right" id="userDropdownMenu" style="display: none;">
                <div class="dropdown-item">
                    <small>Вы вошли как:</small><br>
                    <strong><?= Yii::$app->user->isGuest ? 'Гость' : Yii::$app->user->identity->username ?></strong>
                </div>
                <div class="dropdown-divider"></div>
                <div class="dropdown-footer">
                    <?= \yii\helpers\Html::beginForm(['/site/logout'], 'post') ?>
                    <?= \yii\helpers\Html::submitButton(
                        'Выйти',
                        ['class' => 'btn btn-default btn-flat', 'style' => 'width:100%;']
                    ) ?>
                    <?= \yii\helpers\Html::endForm() ?>
                </div>
            </div>
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

                    <li class="nav-header">Статистики</li>

                    <!-- Заказы -->
                    <li class="nav-item">
                        <a href="/orders" class="nav-link">
                            <i class="nav-icon fas fa-shopping-cart"></i>
                            <p>Заказы</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="/statistics/partner" class="nav-link">
                            <i class="nav-icon fas fa-user-friends"></i>
                            <p>Партнеры</p>
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

                    <li class="nav-item">
                        <?= Html::a('<i class="nav-icon fas fa-flag"></i><p>Страны</p>', ['/country'], ['class' => 'nav-link']) ?>
                    </li>
                    <li class="nav-item">
                        <?= Html::a('<i class="nav-icon fas fa-briefcase"></i><p>Офферы</p>', ['/offer'], ['class' => 'nav-link']) ?>
                    </li>
                    <li class="nav-item">
                        <?= Html::a('<i class="nav-icon fas fa-user-secret"></i><p>Вебы</p>', ['/web'], ['class' => 'nav-link']) ?>
                    </li>

                    <!-- Пользователи 
                    <li class="nav-item">
                        <a href="/admin/user/index" class="nav-link">
                            <i class="nav-icon fas fa-users-cog"></i>
                            <p>Пользователи</p>
                        </a>
                    </li> -->

                    <li class="nav-header">Системные</li>

                    <!-- Логи -->
                    <li class="nav-item">
                        <?= Html::a('<i class="nav-icon fas fa-file-alt"></i><p>Логи</p>', ['/logs'], ['class' => 'nav-link']) ?>
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
        <center>
        <strong>© m4leads.org • <?= date('Y') ?></strong>
        </center>
    </footer>
</div>

<?php
$js = <<<JS
    $(document).ready(function () {
    const dropdownToggle = $('#userDropdown');
    const dropdownMenu = $('#userDropdownMenu');

    dropdownToggle.on('click', function (e) {
        e.preventDefault();
        dropdownMenu.toggle();
    });

    $(document).on('click', function (e) {
        if (!dropdownToggle.is(e.target) && !dropdownToggle.has(e.target).length && !dropdownMenu.is(e.target) && !dropdownMenu.has(e.target).length) {
            dropdownMenu.hide();
        }
    });
});
JS;
$this->registerJs($js);
?>
<?php $this->endBody() ?>

<!-- AdminLTE App -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/js/adminlte.min.js "></script>
</body>
</html>
<?php $this->endPage() ?>