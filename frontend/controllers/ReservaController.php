<?php
namespace frontend\controllers;

use Yii;
use frontend\models\Reserva; 
use frontend\models\Vehiculo;
use yii\web\Controller;
use yii\web\Response;
use yii\widgets\ActiveForm;

class ReservaController extends Controller
{
    public function actionCreate()
    {
        $model = new Reserva();
        $vehiculos = Vehiculo::getVehiculosDisponibles();

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                Yii::$app->session->setFlash('success', '¡Reserva creada exitosamente!');
                return $this->refresh();
            }
        }

        return $this->render('create', [
            'model' => $model,
            'vehiculos' => $vehiculos,
        ]);
    }
}