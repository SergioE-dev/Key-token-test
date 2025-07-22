<?php
namespace frontend\modules\admin;

class Module extends \yii\base\Module
{
    public $controllerNamespace = 'frontend\modules\admin\controllers';
    
    public function init()
    {
        parent::init();
        
        \Yii::$app->urlManager->addRules([
            [
                'class' => 'yii\rest\UrlRule',
                'controller' => 'admin/reserva',
                'extraPatterns' => [
                    'POST' => 'create',
                ]
            ],
        ], false);
    }
}