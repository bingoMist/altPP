<?php

use yii\helpers\Html;
use yii\web\View;

//$this->title = 'Панель управления';
$this->registerJsFile('@web/assets/35df895c/jquery.js', ['position' => View::POS_HEAD]);
$this->registerJsFile('@web/assets/28675b85/yii.js', ['position' => View::POS_HEAD]);
$this->registerJsFile('@web/assets/28675b85/yii.gridView.js', ['position' => View::POS_HEAD]);
$this->registerJsFile('https://cdn.jsdelivr.net/npm/chart.js ', ['position' => \yii\web\View::POS_HEAD]);
?>
<div class="site-index">
    <div class="row">
        <!-- Заказы за сегодня -->
        <div class="col-lg-2 col-xs-6">
            <div class="small-box bg-blue">
                <div class="inner">
                    <h3><?= number_format($ordersCount, 0, '', ' ') ?></h3>
                    <p>Всего заказов за сегодня</p>
                </div>
                <div class="icon">
                    <i class="ion ion-bag"></i>
                </div>
            </div>
        </div>

        <!-- Одобрено -->
        <div class="col-lg-2 col-xs-6">
            <div class="small-box bg-green">
                <div class="inner">
                    <h3><?= number_format($approvedCount, 0, '', ' ') ?></h3>
                    <p>Одобрено</p>
                </div>
                <div class="icon">
                    <i class="ion ion-checkmark-circled"></i>
                </div>
            </div>
        </div>

                <!-- В ожидании -->
        <div class="col-lg-2 col-xs-6">
            <div class="small-box bg-aqua">
                <div class="inner">
                    <h3><?= number_format($pendingCount, 0, '', ' ') ?></h3>
                    <p>В ожидании</p>
                </div>
                <div class="icon">
                    <i class="ion ion-clock"></i>
                </div>
            </div>
        </div>

        <!-- Некорректные -->
        <div class="col-lg-2 col-xs-6">
            <div class="small-box bg-yellow">
                <div class="inner">
                    <h3><?= number_format($invalidCount, 0, '', ' ') ?></h3>
                    <p>Некорректные</p>
                </div>
                <div class="icon">
                    <i class="ion ion-alert-circled"></i>
                </div>
            </div>
        </div>

        <!-- Отменённые -->
        <div class="col-lg-2 col-xs-6">
            <div class="small-box bg-red">
                <div class="inner">
                    <h3><?= number_format($cancelledCount, 0, '', ' ') ?></h3>
                    <p>Отменённые</p>
                </div>
                <div class="icon">
                    <i class="ion ion-close-circled"></i>
                </div>
            </div>
        </div>

        <!-- Дубли -->
        <div class="col-lg-2 col-xs-6">
            <div class="small-box" style="background-color: #000; color: white;">
                <div class="inner">
                    <h3><?= number_format($duplicatesCount, 0, '', ' ') ?></h3>
                    <p>Дубли</p>
                </div>
                <div class="icon">
                    <i class="ion ion-document-duplicate"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <h3>Активность за сегодня</h3>
        <canvas id="activityChart" height="100"></canvas>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js "></script>

<script>
    const ctx = document.getElementById('activityChart').getContext('2d');

    const activityData = <?= $activityData ?>;

    const labels = [];
    for (let i = 0; i < 24; i++) {
        labels.push(i + ':00-' + i + ':59');
    }

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Одобренные заказы',
                data: activityData,
                backgroundColor: '#4285F4',
                borderRadius: 4,
                barPercentage: 0.9,
                categoryPercentage: 0.95
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.parsed.y + ' заказов';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                },
                x: {
                    ticks: {
                        autoSkip: false,
                        maxRotation: 0,
                        minRotation: 0
                    }
                }
            }
        }
    });
</script>