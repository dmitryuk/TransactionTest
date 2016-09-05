<?php

namespace app\models;

use Yii;
use yii\base\Model;


class TransactionForm extends Model
{

    public $id_sender;
    public $id_requester;
    public $sum;


    public function rules()
    {
        return [
            // username and password are both required
            [['id_sender','id_requester','sum'], 'required','message'=>'Обязательно для заполнения'],
            ['id_sender','compare','compareAttribute'=>'id_requester','operator'=>'!=','message'=>'Нельзя перечислять
            средства самому себе'],
            ['sum','double','min'=>0.1]
        ];
    }

    public function Validate()
    {
        $sender = Users::findOne(['id'=>$this->id_sender]);
        $requester = Users::findOne(['id'=>$this->id_requester]);

        if(!$sender||!$requester) return false;
        if($sender['balance']<$this->sum){
            $this->addError('error','Баланс отправителя меньше '.$this->sum);
            return false;
        }
        return $this->makeTransaction($sender,$requester,$this->sum);
    }

    /**
     * @param $sender Users
     * @param $requester Users
     * @param $sum double
     */
    private function makeTransaction($sender,$requester,$sum)
    {
        $sender->balance -= $sum;

        $requester->balance += $sum;
        if($requester->save()&&$sender->save())
            return true;
        else{
            $this->addError('error','Ошибка транзакции');
            return false;
        }
    }



    public function attributeLabels()
    {
        return [
            'id_sender' => 'Отправитель',
            'id_requester' => 'Получатель',
            'sum'=>'Сумма перевода'
        ];
    }
}
