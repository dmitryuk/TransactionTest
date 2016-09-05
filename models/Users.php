<?php

namespace app\models;


class Users extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'users';
    }


    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Имя',
            'balance' => 'Баланс'
        ];
    }

    public function rules()
    {
        return [


            [['name'], 'string', 'max' => 100],
            [['name', 'id'], 'required'],
            [['name'], 'unique'],

        ];
    }

}
