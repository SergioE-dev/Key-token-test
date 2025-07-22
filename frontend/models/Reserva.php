<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "reservas".
 *
 * @property int $id
 * @property string $nombre
 * @property string $apellido
 * @property string $rut
 * @property string $email
 * @property string $telefono
 * @property int $vehiculo_id
 * @property string|null $fecha_reserva
 */
class Reserva extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'reservas';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nombre', 'apellido', 'rut', 'email', 'telefono', 'vehiculo_id'], 'required'],
            [['vehiculo_id'], 'integer'],
            [['fecha_reserva'], 'safe'],
            ['rut', 'validateRut'],
            [['nombre', 'apellido'], 'string', 'max' => 50],
            [['telefono'], 'string', 'max' => 15],
            [['email'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nombre' => 'Nombre',
            'apellido' => 'Apellido',
            'rut' => 'Rut',
            'email' => 'Email',
            'telefono' => 'Telefono',
            'vehiculo_id' => 'Vehiculo ID',
            'fecha_reserva' => 'Fecha Reserva',
        ];
    }

    // Validación personalizada para RUT chileno
    public function validateRut($attribute, $params)
    {
        $rut = $this->$attribute;
        if (!preg_match('/^(\d{1,3}(?:\.?\d{3}){2}-[\dkK])$/', $rut)) {
            $this->addError($attribute, 'Formato de RUT inválido. Use: 12.345.678-9');
            return;
        }

        list($numero, $dv) = explode('-', str_replace('.', '', $rut));
        $dv = strtoupper($dv);
        $i = 2;
        $suma = 0;

        foreach (array_reverse(str_split($numero)) as $v) {
            if ($i == 8) $i = 2;
            $suma += $v * $i;
            $i++;
        }

        $dvr = 11 - ($suma % 11);
        if ($dvr == 11) $dvr = '0';
        if ($dvr == 10) $dvr = 'K';

        if ($dvr != $dv) {
            $this->addError($attribute, 'RUT inválido. Verifique el dígito verificador.');
        }
    }

    public function getVehiculo()
    {
        return $this->hasOne(Vehiculo::className(), ['id' => 'vehiculo_id']);
    }

    // Asegúrate que los campos son seguros para asignación masiva
    public function fields()
    {
        return [
             'id',
            'nombre',
            'apellido',
            'rut',
            'email',
            'telefono',
            'vehiculo_id',
            'fecha_reserva',
            'fecha_formateada' => function () {
                return Yii::$app->formatter->asDatetime($this->fecha_reserva);
            }
        ];
    }
}
