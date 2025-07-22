<?php

/* @var $this yii\web\View */
/* @var $labels string */ // Asegúrate de que esta línea esté presente
/* @var $data string */   // Asegúrate de que esta línea esté presente

use yii\helpers\Html;

$this->title = 'Reservas por Tipo de Vehículo';
$this->params['breadcrumbs'][] = $this->title;

// Registro del asset de Chart.js
// Puedes descargarlo y ponerlo en tus assets locales, o usar un CDN
$this->registerJsFile('https://cdn.jsdelivr.net/npm/chart.js', ['position' => \yii\web\View::POS_HEAD]);
?>

<div class="reserva-grafico">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        Este gráfico muestra la cantidad de reservas para cada tipo de vehículo (identificado por su ID).
    </p>

    <div style="width: 70%; margin: auto;">
        <canvas id="myChart"></canvas>
    </div>

<?php
// Script JavaScript para renderizar el gráfico con Chart.js
$script = <<<JS
    var ctx = document.getElementById('myChart').getContext('2d');
    var labels = $labels; // PHP ya lo ha codificado a JSON
    var data = $data;     // PHP ya lo ha codificado a JSON

    var myChart = new Chart(ctx, {
        type: 'bar', // Puedes cambiar a 'pie', 'doughnut', 'line', etc.
        data: {
            labels: labels,
            datasets: [{
                label: 'Número de Reservas',
                data: data,
                backgroundColor: [
                    'rgba(255, 99, 132, 0.6)',
                    'rgba(54, 162, 235, 0.6)',
                    'rgba(255, 206, 86, 0.6)',
                    'rgba(75, 192, 192, 0.6)',
                    'rgba(153, 102, 255, 0.6)',
                    'rgba(255, 159, 64, 0.6)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Cantidad de Reservas'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'ID de Vehículo'
                    }
                }
            },
            responsive: true,
            plugins: {
                legend: {
                    display: false // No mostrar leyenda si solo hay un dataset
                },
                title: {
                    display: true,
                    text: 'Conteo de Reservas por ID de Vehículo'
                }
            }
        }
    });
JS;
$this->registerJs($script, \yii\web\View::POS_END);
?>
</div>