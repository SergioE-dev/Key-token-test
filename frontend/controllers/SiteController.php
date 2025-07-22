<?php

namespace frontend\controllers;

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
/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => \yii\web\ErrorAction::class,
            ],
            'captcha' => [
                'class' => \yii\captcha\CaptchaAction::class,
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $model = new Reserva();
        $vehiculos = Vehiculo::getVehiculosDisponibles();
        
        // Validación AJAX
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        
        // Procesamiento del formulario
        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                Yii::$app->session->setFlash('success', '¡Reserva creada exitosamente!');
                return $this->refresh();
            } else {
                Yii::$app->session->setFlash('error', 'Error al guardar la reserva. Por favor verifica los datos.');
            }
        }

        // Api
        
        //Api
        return $this->render('index', [
            'model' => $model,
            'vehiculos' => $vehiculos,
        ]);
    }

    /**
     * Logs in a user.
     *
     * @return mixed
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';

        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logs out the current user.
     *
     * @return mixed
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return mixed
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail(Yii::$app->params['adminEmail'])) {
                Yii::$app->session->setFlash('success', 'Thank you for contacting us. We will respond to you as soon as possible.');
            } else {
                Yii::$app->session->setFlash('error', 'There was an error sending your message.');
            }

            return $this->refresh();
        }

        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return mixed
     */
    // public function actionAbout()
    // {
    //     return $this->render('about');
    // }

    /**
     * Signs user up.
     *
     * @return mixed
     */
    public function actionSignup()
    {
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post()) && $model->signup()) {
            Yii::$app->session->setFlash('success', 'Thank you for registration. Please check your inbox for verification email.');
            return $this->goHome();
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');

                return $this->goHome();
            }

            Yii::$app->session->setFlash('error', 'Sorry, we are unable to reset password for the provided email address.');
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'New password saved.');

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }

    /**
     * Verify email address
     *
     * @param string $token
     * @throws BadRequestHttpException
     * @return yii\web\Response
     */
    public function actionVerifyEmail($token)
    {
        try {
            $model = new VerifyEmailForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
        if (($user = $model->verifyEmail()) && Yii::$app->user->login($user)) {
            Yii::$app->session->setFlash('success', 'Your email has been confirmed!');
            return $this->goHome();
        }

        Yii::$app->session->setFlash('error', 'Sorry, we are unable to verify your account with provided token.');
        return $this->goHome();
    }

    /**
     * Resend verification email
     *
     * @return mixed
     */
    public function actionResendVerificationEmail()
    {
        $model = new ResendVerificationEmailForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');
                return $this->goHome();
            }
            Yii::$app->session->setFlash('error', 'Sorry, we are unable to resend verification email for the provided email address.');
        }

        return $this->render('resendVerificationEmail', [
            'model' => $model
        ]);
    }

    /**
     * Displays about page with vehicle reservation chart.
     * @return string
     */
    public function actionAbout()
    {
        // URL de tu API de reservas
        $apiUrl = 'http://dev.reserva.front/index.php?r=api/reserva';
        $httpClient = new Client();
        $vehiculoCounts = []; // Array para almacenar el conteo de cada vehiculo_id

        try {
            // 1. Consumir la API
            $response = $httpClient->get($apiUrl)->send();

            // Verificar si la respuesta HTTP fue exitosa (código 2xx)
            if ($response->isOk) {
                $rawContent = $response->content; // Obtener el contenido como string

                // --- DEBUGGING: Siempre es útil ver qué se recibe ---
                Yii::debug('Contenido de la API (JSON): ' . $rawContent, __METHOD__);
                echo "<pre>Debugging Raw API Content (JSON):\n";
                echo htmlentities($rawContent); // Usa htmlentities para ver el HTML si lo hay
                echo "</pre>";
                // ----------------------------------------------------

                // 2. Parsear el JSON
                $jsonData = json_decode($rawContent, true); // true para decodificar como array asociativo

                if (json_last_error() !== JSON_ERROR_NONE) {
                    // JSON malformado o no es JSON
                    Yii::error('Error al parsear el JSON de la API de reservas. Contenido recibido: ' . $rawContent . ' Error: ' . json_last_error_msg());
                    Yii::$app->session->setFlash('error', 'No se pudieron cargar los datos de las reservas. El formato de la respuesta JSON no es válido.');
                    return $this->render('error', [
                        'name' => 'Error de formato de datos (JSON)',
                        'message' => 'La API de reservas no devolvió un JSON válido. Revise los logs para más detalles.'
                    ]);
                }

                // 3. Procesar los datos: Contar las reservas por vehiculo_id
                // Acceder a la clave 'reservas' del array JSON
                if (isset($jsonData['reservas']) && is_array($jsonData['reservas'])) {
                    foreach ($jsonData['reservas'] as $item) {
                        // Asegurarse de que 'vehiculo_id' existe en cada item
                        if (isset($item['vehiculo_id'])) {
                            $vehiculoId = (string)$item['vehiculo_id'];
                            if (!isset($vehiculoCounts[$vehiculoId])) {
                                $vehiculoCounts[$vehiculoId] = 0;
                            }
                            $vehiculoCounts[$vehiculoId]++;
                        } else {
                            Yii::warning('Item de reserva sin "vehiculo_id" en la respuesta JSON: ' . json_encode($item));
                        }
                    }
                } else {
                    // No hay 'reservas' o no es un array en la respuesta JSON
                    Yii::warning('No se encontraron datos de reservas válidos en la respuesta JSON de la API. JSON: ' . $rawContent);
                    Yii::$app->session->setFlash('info', 'La API no devolvió datos de reservas para mostrar el gráfico.');
                    // Si no hay datos, inicializa variables vacías para que el gráfico no falle
                    $labels = [];
                    $data = [];
                    return $this->render('about', [
                        'labels' => json_encode($labels),
                        'data' => json_encode($data),
                    ]);
                }

            } else {
                // La API devolvió un código de estado HTTP de error (4xx, 5xx)
                Yii::error('Error al obtener datos de la API de reservas. Código de estado: ' . $response->statusCode . '. Contenido: ' . $response->content);
                Yii::$app->session->setFlash('error', 'No se pudo conectar con la API de reservas. Código de estado: ' . $response->statusCode);
                return $this->render('error', [
                    'name' => 'Error de conexión con la API',
                    'message' => 'No se pudo obtener la información de reservas. Por favor, inténtelo de nuevo más tarde. Código: ' . $response->statusCode
                ]);
            }

        } catch (\Exception $e) {
            // Captura cualquier otra excepción durante el proceso (problemas de red, etc.)
            Yii::error('Excepción al consumir la API de reservas: ' . $e->getMessage());
            Yii::$app->session->setFlash('error', 'Ocurrió un error inesperado al procesar las reservas.');
            return $this->render('error', [
                'name' => 'Error interno del servidor',
                'message' => 'Ocurrió un error inesperado al procesar las reservas: ' . $e->getMessage()
            ]);
        }

        // 4. Preparar datos para el gráfico (si todo salió bien)
        $labels = array_keys($vehiculoCounts);   // vehiculo_id como etiquetas
        $data = array_values($vehiculoCounts); // conteo como datos

        // Opcional: Si tienes una tabla de vehículos en tu DB, podrías mapear vehiculo_id a nombres reales
        // Ejemplo:
        /*
        $vehiculos = \common\models\Vehiculo::find()->select(['id', 'nombre'])->indexBy('id')->asArray()->all();
        $labels = array_map(function($id) use ($vehiculos) {
            return $vehiculos[$id]['nombre'] ?? 'Vehículo ID: ' . $id;
        }, $labels);
        */

        // 5. Renderizar la vista 'about.php', pasando las variables labels y data
        return $this->render('about', [
            'labels' => json_encode($labels), // Convertir a JSON para pasar a JavaScript
            'data' => json_encode($data),     // Convertir a JSON para pasar a JavaScript
        ]);
    }
    
}
