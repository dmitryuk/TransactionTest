<?php

namespace app\models;

use Yii;
use yii\base\Exception;
use yii\base\Model;


class TransactionForm extends Model
{

    public $id_sender;
    public $id_requester;
    public $sum;
    private $sender;
    private $requester;

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
        $this->sender = Users::findOne(['id' => $this->id_sender]);
        $this->requester = Users::findOne(['id' => $this->id_requester]);

        if (!$this->sender || !$this->requester) return false;
        //Баланс отправителя должен быть больше отправляемой суммы
        if ($this->sender->balance < $this->sum) {
            $this->addError('error', 'Баланс отправителя меньше ' . $this->sum);
            return false;
        }
        return true;
    }

    /**
     * Запускаем транзацию
     * @param $sender Users Отправитель
     * @param $requester Users Получатель
     * @param $sum double Сумма
     */
    public function makeTransaction()
    {


        $transaction = \yii::$app->db->beginTransaction();

        try {
            $sender = \yii::$app->db->createCommand("select `id`,`balance` from `users` where `id`=:id_sender   FOR UPDATE",
                [':id_sender' => $this->id_sender]
            )->queryOne();
            $requester = \yii::$app->db->createCommand("select `id`,`balance` from `users` where `id`=:id_requester  FOR UPDATE",
                [':id_requester' => $this->id_requester]
            )->queryOne();
            if (!$sender || !$requester)
                throw new \Exception('Отправитель или получатель не существует');

            $q1 = \yii::$app->db->createCommand("Update `users` set `balance`=`balance`+:balance where `id`=:id", ['balance' => $this->sum, 'id' => $sender['id']])->execute();
            $q2 = \yii::$app->db->createCommand("Update `users` set `balance`=`balance`-:balance where `id`=:id", ['balance' => $this->sum, 'id' => $requester['id']])->execute();

            if (!$q1 || !$q2)
                throw new \Exception('Ошибка сохрания баланса');
            $transaction->commit();
            return true;
        } catch (\Exception $e) {
            $transaction->rollBack();
            $this->addError('error', $e->getMessage());
        }
        return false;
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
