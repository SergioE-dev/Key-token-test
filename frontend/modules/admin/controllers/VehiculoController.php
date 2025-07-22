<?php

namespace frontend\modules\admin\controllers;
use frontend\models\vehiculo;
use Yii;
use yii\httpclient\Client; // Importar el cliente HTTP de Yii2

use yii\rest\ActiveController;

class vehiculoController extends ActiveController{

    public $modelClass = 'frontend\models\vehiculo';

    public $serializer = [
        'class' => 'yii\rest\Serializer',
        'collectionEnvelope' => 'vehiculos',
    ];

    public function init()
    {
        parent::init();
        // \Yii::$app->enableSession = false; // Disable session for API
    }
    
    
}