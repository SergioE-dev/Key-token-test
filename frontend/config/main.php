<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-frontend',
    'name' => 'Reservas App',
    'timezone' => 'America/Santiago',
    // set target language to be Spanish
    'language' => 'es-ES',
    //API
    'homeUrl' => '',
    // set source language to be English
    'sourceLanguage' => 'en-US',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'frontend\controllers',
    //API
    'modules' => [
        'api' => [
            'class' => 'frontend\modules\admin\Api',
        ],
    ],
    //API
    'components' => [
        'formatter' =>
        [
            'class' => \yii\i18n\Formatter::className(),
            'dateFormat' => 'dd-MM-yyyy',
            'datetimeFormat' => 'dd-MM-yyyy HH:mm:ss',
            'decimalSeparator' => ',',
            'thousandSeparator' => '.',
            // 'decimals' => 2,
            //'numberFormatterOptions' =>[
            //    NumberFormatter::MIN_FRACTION_DIGITS => 0,
            //    NumberFormatter::MAX_FRACTION_DIGITS => 2,
            //],
            'currencyCode' => 'CLP',
            'locale' => 'es_CL',
            //'as rutFormatter' => \sateler\rut\RutFormatBehavior::className(),
        ],
        'request' => [
            'csrfParam' => '_csrf-frontend',
            'cookieValidationKey' => 'YOUR_COOKIE_VALIDATION_KEY', // Replace with your actual key
            //API
            'baseUrl' => '',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
            //
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-frontend', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the frontend
            'name' => 'advanced-frontend',
            'savePath' => sys_get_temp_dir(),
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => \yii\log\FileTarget::class,
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],


        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false, // Cambiar a false si no se usa index.php en las URLs
            'rules' => [
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'api/reserva',
                    'extraPatterns' => [
                        'GET index' => 'index',
                        'POST create' => 'create', // Asegúrate que esta línea existe
                        'DELETE {id}' => 'delete',
                        'GET,OPTIONS generate-api-key' => 'generate-api-key',
                        // 'GET api-key' => 'api-key',
                    ],
                    'pluralize' => false,
                ],

            ],
        ], //modificar o restaurar

    ],
    'params' => $params,
];
