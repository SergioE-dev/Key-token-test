<?php

namespace frontend\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "api_key".
 *
 * @property int $id
 * @property int $user_id
 * @property string $api_key
 * @property string $created_at
 * @property string|null $expires_at
 * @property string|null $status
 */
class ApiKey extends \yii\db\ActiveRecord
{

    /**
     * ENUM field values
     */
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_REVOKED = 'revoked';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'api_key';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            // [['expires_at'], 'default', 'value' => null],
            // [['status'], 'default', 'value' => 'active'],
            // [['user_id', 'api_key', 'created_at'], 'required'],
            // [['user_id'], 'integer'],
            // [['created_at', 'expires_at'], 'safe'],
            // [['status'], 'string'],
            // [['api_key'], 'string', 'max' => 100],
            // ['status', 'in', 'range' => array_keys(self::optsStatus())],
            // [['api_key'], 'unique'],
            [['api_key', 'created_at'], 'required'],
            [['user_id'], 'integer'],
            [['created_at', 'expires_at'], 'datetime', 'format' => 'php:Y-m-d H:i:s'],
            [['status'], 'in', 'range' => ['active', 'inactive', 'revoked']],
            [['api_key'], 'string', 'max' => 255],
            [['api_key'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'api_key' => 'Api Key',
            'created_at' => 'Created At',
            'expires_at' => 'Expires At',
            'status' => 'Status',
        ];
    }


    /**
     * column status ENUM value labels
     * @return string[]
     */
    public static function optsStatus()
    {
        return [
            self::STATUS_ACTIVE => 'active',
            self::STATUS_INACTIVE => 'inactive',
            self::STATUS_REVOKED => 'revoked',
        ];
    }

    /**
     * @return string
     */
    public function displayStatus()
    {
        return self::optsStatus()[$this->status];
    }

    /**
     * @return bool
     */
    public function isStatusActive()
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function setStatusToActive()
    {
        $this->status = self::STATUS_ACTIVE;
    }

    /**
     * @return bool
     */
    public function isStatusInactive()
    {
        return $this->status === self::STATUS_INACTIVE;
    }

    public function setStatusToInactive()
    {
        $this->status = self::STATUS_INACTIVE;
    }

    /**
     * @return bool
     */
    public function isStatusRevoked()
    {
        return $this->status === self::STATUS_REVOKED;
    }

    public function setStatusToRevoked()
    {
        $this->status = self::STATUS_REVOKED;
    }

    // Apikey generation
    public function generateApiKey()
    {
        $this->api_key = Yii::$app->security->generateRandomString(32);
        $this->created_at = date('Y-m-d H:i:s');
        $this->status = 'active';
        return $this->api_key;
    }

    public static function findByApiKey($apiKey)
    {
        return static::findOne([
            'api_key' => $apiKey,
            'status' => 'active',
        ]);
    }
    // Apikey generation

}
