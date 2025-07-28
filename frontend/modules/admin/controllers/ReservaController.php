<?php

namespace frontend\modules\admin\controllers;

use frontend\models\Reserva;
use frontend\models\ApiKey;
use Yii;
use yii\rest\ActiveController;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\QueryParamAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\VerbFilter;
use yii\filters\ContentNegotiator;
use yii\filters\Cors;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UnauthorizedHttpException;


class ReservaController extends ActiveController
{
    public $modelClass = 'frontend\models\Reserva';

    public $serializer = [
        'class' => 'yii\rest\Serializer',
        'collectionEnvelope' => 'reservas',
    ];

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        // Forzar JSON para todas las respuestas
        $behaviors['contentNegotiator'] = [
            'class' => ContentNegotiator::class,
            'formats' => [
                'application/json' => Response::FORMAT_JSON,
            ],
        ];

        // Configuración CORS (si es necesaria)
        $behaviors['corsFilter'] = [
            'class' => Cors::class,
            'cors' => [
                'Origin' => ['*'],
                'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
                'Access-Control-Request-Headers' => ['*'],
            ],
        ];

        // token y api-key

        // Configuración del autenticador (cambia a HttpBearerAuth)
        $behaviors['authenticator'] = [
            // 'class' => \yii\filters\auth\HttpBearerAuth::class,
            'class' => CompositeAuth::class,
            'authMethods' => [
                HttpBearerAuth::class,
                QueryParamAuth::class,
                CustomQueryParamAuth::class, // Asegúrate de que esta clase esté definida
                'tokenParam' => 'api_key', // Parámetro de consulta: ?api_key=tu_clave
            ],
            'only' => ['index', 'create', 'update', 'delete'], // Asegúrate de que estas acciones requieren autenticación
            'except' => ['options', 'generate-api-key'], // Excluir generate-api-key de autenticación
            // 'except' => ['create', 'options'], // Excluye el método OPTIONS para CORS
        ];
        // token
        // Configuración del VerbFilter (CORRECTO)
        $behaviors['verbFilter'] = [
            'class' => VerbFilter::class,
            'actions' => [
                'index' => ['GET', 'HEAD'],
                'view' => ['GET', 'HEAD'],
                'create' => ['POST', 'OPTIONS'], // Asegúrate que POST está permitido
                'update' => ['PUT', 'PATCH', 'OPTIONS'],
                'delete' => ['DELETE', 'OPTIONS'],
                // 'generate-api-key' => ['GET', 'OPTIONS'], // Permitir GET para generar claves
            ],
        ];

        return $behaviors;
    }

    // Api-key
    /**
     * Validar la API key
     */
    public function authenticate($user, $request, $response)
    {
        $apiKey = $request->get('api_key', $request->headers->get('Authorization'));
        if (strpos($apiKey, 'Bearer ') === 0) {
            $apiKey = substr($apiKey, 7); // Extraer el token después de "Bearer "
        }

        // Validar contra la base de datos
        $key = \frontend\models\ApiKey::findOne([
            'api_key' => $apiKey,
            'status' => 'active',
        ]);

        if ($key) {
            // Opcional: asociar un usuario si la clave está vinculada a uno
            $user = \app\models\User::findOne($key->user_id);
            return $user;
        }

        throw new UnauthorizedHttpException('Invalid or inactive API key.');
    }
    // Api-key


    public function actions()
    {
        $actions = parent::actions();
        unset($actions['create']); // Eliminar la acción create por defecto

        // Personalizar la acción index (lista de reservas)
        $actions['index']['prepareDataProvider'] = function () {
            return new \yii\data\ActiveDataProvider([
                'query' => Reserva::find(),
                'pagination' => [
                    'pageSize' => 20,
                ],
            ]);
        };

        return $actions;
    }

    public function afterAction($action, $result)
    {
        $result = parent::afterAction($action, $result);

        // Formatear fechas u otros campos si es necesario
        if (is_array($result) && isset($result['reservas'])) {
            foreach ($result['reservas'] as &$reserva) {
                if (isset($reserva['fecha_reserva'])) {
                    $reserva['fecha_formateada'] = Yii::$app->formatter->asDatetime($reserva['fecha_reserva']);
                }
            }
        }

        return $result;
    }



    // Esta acción debe llamarse actionCreate (no create)
    public function actionCreate()
    {
        Yii::info('Llegó a actionCreate', __METHOD__); // Registro para depuración
        $model = new Reserva();

        if ($model->load(Yii::$app->request->getBodyParams(), '') && $model->save()) {
            Yii::$app->response->statusCode = 201;
            return $model;
        } else {
            Yii::$app->response->statusCode = 422;
            return ['errors' => $model->errors];
        }
    }
    public function actionDelete($id)
    {
        Yii::info('Llegó a actionDelete', __METHOD__);
        $model = $this->findModel($id);
        if ($model->delete()) {
            Yii::$app->response->statusCode = 204; // No Content
            return null;
        } else {
            Yii::$app->response->statusCode = 422;
            return ['errors' => $model->errors];
        }
    }

    protected function findModel($id)
    {
        if (($model = Reserva::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('La reserva no existe.');
    }

    public function actionGenerateApiKey()
    {
        $apiKey = new ApiKey();
        $apiKey->generateApiKey();
        if ($apiKey->save()) {
            return [
                'api_key' => $apiKey->api_key,
                'created_at' => $apiKey->created_at,
                'status' => $apiKey->status,
            ];
        } else {
            Yii::$app->response->statusCode = 422;
            return ['errors' => $apiKey->errors];
        }
    }
}
class CustomQueryParamAuth extends QueryParamAuth
{
    public $tokenParam = 'api_key';

    public function authenticate($user, $request, $response)
    {
        $apiKey = $request->get($this->tokenParam);
        if ($apiKey) {
            $apiKeyModel = ApiKey::findByApiKey($apiKey);
            if ($apiKeyModel !== null) {
                // Verificar si la clave no ha expirado
                if ($apiKeyModel->expires_at === null || strtotime($apiKeyModel->expires_at) > time()) {
                    return true; // Autenticación exitosa
                }
                throw new UnauthorizedHttpException('La clave API ha expirado.');
            }
        }
        throw new UnauthorizedHttpException('Clave API inválida o no proporcionada.');
    }
}
// Aquí se configuró para mostrar las reservas con un formato específico y se manejó la creación de reservas con validación de datos. También se puede agregar entradas a la api con el verification_token y se configuró el comportamiento de CORS para permitir solicitudes desde diferentes orígenes.
