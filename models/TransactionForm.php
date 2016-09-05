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
            [['id_sender', 'id_requester', 'sum'], 'required', 'message' => 'Обязательно для заполнения'],
            ['id_sender', 'compare', 'compareAttribute' => 'id_requester', 'operator' => '!=', 'message' => 'Нельзя перечислять
            средства самому себе'],
            ['sum', 'double', 'min' => 0.1]
        ];
    }

    /**
     * @return bool Проверяем транзакцию
     */
    public function Validate()
    {
        $sender = Users::findOne(['id' => $this->id_sender]);
        $requester = Users::findOne(['id' => $this->id_requester]);

        if (!$sender || !$requester) return false;
        //Баланс отправителя должен быть больше отправляемой суммы
        if ($sender['balance'] < $this->sum) {
            $this->addError('error', 'Баланс отправителя меньше ' . $this->sum);
            return false;
        }
        return $this->makeTransaction($sender, $requester, $this->sum);
    }

    /**
     * Запускаем транзацию
     * @param $sender Users Отправитель
     * @param $requester Users Получатель
     * @param $sum double Сумма
     */
    private function makeTransaction($sender, $requester, $sum)
    {
        $sender->balance -= $sum;

        $requester->balance += $sum;
        if ($requester->save() && $sender->save())
            return true;
        else {
            $this->addError('error', 'Ошибка транзакции');
            return false;
        }
    }


    public function attributeLabels()
    {
        return [
            'id_sender' => 'Отправитель',
            'id_requester' => 'Получатель',
            'sum' => 'Сумма перевода'
        ];
    }
}
