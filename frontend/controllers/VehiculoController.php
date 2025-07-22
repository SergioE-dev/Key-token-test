<?php

namespace frontend\controllers;

use yii\rest\ActiveController;
use yii\filters\ContentNegotiator;

use frontend\models\ResendVerificationEmailForm;
use frontend\models\VerifyEmailForm;
use frontend\models\Reserva;
use frontend\models\Vehiculo;
use yii\web\Response;
use yii\widgets\ActiveForm;
use Yii;
use yii\base\InvalidArgumentException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;
use yii\httpclient\Client; // Importar el cliente HTTP de Yii2
use yii\filters\auth\HttpBasicAuth; // Opcional: Para añadir autenticación básica
use yii\filters\auth\HttpBearerAuth; // Opcional: Para añadir autenticación con token

class VehiculoController extends ActiveController
{
    // Define el modelo que este controlador API va a gestionar
    public $modelClass = 'frontend\models\vehiculo'; // Asegúrate de que 'app\models\Reserva' sea el namespace completo de tu modelo

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        // Configura la negociación de contenido para que la API siempre devuelva JSON
        $behaviors['contentNegotiator'] = [
            'class' => ContentNegotiator::class,
            'formats' => [
                'application/json' => Response::FORMAT_JSON,
            ],
        ];

        // --- Opcional: Autenticación (Elige uno o combínalos según tu necesidad) ---
        // Si necesitas autenticación para tu API
        /*
        // Autenticación HTTP Basic (usuario y contraseña en cada petición)
        $behaviors['authenticator'] = [
            'class' => HttpBasicAuth::class,
            'auth' => function ($username, $password) {
                // Aquí debes implementar tu lógica para validar el usuario y la contraseña.
                // Por ejemplo, buscar el usuario en tu tabla de usuarios.
                $user = \app\models\User::findByUsername($username); // Asume que tienes un modelo User
                if ($user && $user->validatePassword($password)) {
                    return $user;
                }
                return null;
            },
        ];
        */

        /*
        // Autenticación HTTP Bearer (token en el encabezado Authorization)
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
            // Puedes especificar el método de autenticación. Por defecto, busca en la cabecera Authorization: Bearer <token>
            // 'challengeCallback' => function ($response, $authMethod) { ... } // Personaliza el desafío de autenticación
        ];
        */
        // --- Fin de Opcional: Autenticación ---

        return $behaviors;
    }

    // --- Opcional: Filtrar campos sensibles de la respuesta ---
    // Si tu modelo Reserva tiene campos que no quieres exponer en la API (ej. claves, hashes, etc.)
    // puedes sobrescribir el método fields() en tu modelo Reserva.
    /*
    // Dentro de app/models/Reserva.php
    public function fields()
    {
        $fields = parent::fields();

        // Elimina campos sensibles
        unset($fields['created_at'], $fields['updated_at']); // Ejemplo: no mostrar estas marcas de tiempo

        // Puedes añadir campos calculados o personalizados
        $fields['duracion_horas'] = function ($model) {
            $start = new \DateTime($model->fecha_inicio);
            $end = new \DateTime($model->fecha_fin);
            $interval = $start->diff($end);
            return $interval->h + ($interval->days * 24);
        };

        return $fields;
    }
    */
    // --- Fin de Opcional: Filtrar campos sensibles ---

    // Puedes sobrescribir acciones individuales si necesitas una lógica personalizada.
    // Por ejemplo, para la acción 'index' (listar todas las reservas)
    /*
    public function actions()
    {
        $actions = parent::actions();

        // Deshabilitar la acción 'delete' por ejemplo
        // unset($actions['delete']);

        // Personalizar la preparación de datos para la acción 'index'
        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];

        return $actions;
    }

    public function prepareDataProvider()
    {
        // Aquí puedes añadir lógica de filtrado, ordenamiento o paginación personalizado
        // Por ejemplo, solo mostrar reservas confirmadas:
        return new \yii\data\ActiveDataProvider([
            'query' => \app\models\Reserva::find()->where(['estado' => 'confirmada']),
            'pagination' => [
                'pageSize' => 10, // 10 reservas por página
            ],
            'sort' => [
                'defaultOrder' => [
                    'fecha_inicio' => SORT_ASC, // Ordenar por fecha de inicio ascendente
                ],
            ],
        ]);
    }
    */
}
