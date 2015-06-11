<?php

namespace common\modules\users\models\backend;

use yii\db\ActiveRecord;
use Yii;

/**
 * @inheritdoc
 */
class User extends \common\modules\users\models\User
{
    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return [
            'create' => ['phone', 'email', 'phone_verify', 'email_verify', 'password', 'group', 'status'],
            'update' => ['phone', 'email', 'phone_verify', 'email_verify', 'password', 'group', 'status'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function transactions()
    {
        return [
            'create' => self::OP_ALL,
            'update' => self::OP_ALL,
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['phone', 'phone_verify', 'email', 'email_verify', 'group', 'status'], 'required'],
            [['phone', 'phone_verify', 'email', 'email_verify', 'group', 'status'], 'trim'],

            [['phone_verify', 'mail_verify'], 'boolean'],

            ['email', 'email'],
            ['email', 'unique', 'targetAttribute' => 'email'],

            ['group', 'in', 'range' => array_keys(\nepster\users\rbac\models\AuthItem::getGroupsArray())],
            ['status', 'in', 'range' => array_keys(self::getStatusArray())],

            ['password', 'required', 'on' => 'create'],
            ['password', 'trim'],
            ['password', 'match', 'pattern' => '/^(?=.*\d)(?=.*[A-Za-z])[0-9A-Za-z-_!@,#$%]{6,16}$/', 'message' => Yii::t('users', 'SIMPLE_PASSWORD')],
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (\nepster\users\models\User::beforeSave($insert)) {
            if (!empty($this->password)) {
                $this->setPassword($this->password);
            } else {
                $this->password = $this->getOldAttribute('password');
            }
            return true;
        }
        return true;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPerson()
    {
        return $this->hasOne(LegalPerson::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProfile()
    {
        return $this->hasOne(Profile::className(), ['user_id' => 'id']);
    }
}
