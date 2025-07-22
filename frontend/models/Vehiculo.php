<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "vehiculos".
 *
 * @property int $id
 * @property string $modelo
 * @property string $marca
 * @property int|null $autonomia
 * @property float|null $precio_hora
 * @property string|null $imagen
 * @property int|null $disponible
 */
class Vehiculo extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'vehiculos';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['autonomia', 'precio_hora', 'imagen'], 'default', 'value' => null],
            [['disponible'], 'default', 'value' => 1],
            [['modelo', 'marca'], 'required'],
            [['autonomia', 'disponible'], 'integer'],
            [['precio_hora'], 'number'],
            [['modelo'], 'string', 'max' => 100],
            [['marca'], 'string', 'max' => 50],
            [['imagen'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'modelo' => 'Modelo',
            'marca' => 'Marca',
            'autonomia' => 'Autonomia',
            'precio_hora' => 'Precio Hora',
            'imagen' => 'Imagen',
            'disponible' => 'Disponible',
        ];
    }

    public static function getVehiculosDisponibles()
    {
        return self::find()->all();
    }

}
