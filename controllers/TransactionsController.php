<?php

namespace app\controllers;

use app\models\LoginForm;
use app\models\TransactionForm;
use app\models\Users;

class TransactionsController extends \yii\web\Controller
{


    public function actionIndex()
    {
        $users = Users::find()->all();

        $model = new TransactionForm();
        if ($model->load(\yii::$app->request->post()) && $model->validate()) {
            return $this->render('success', ['model' => $model]);
        }
        return $this->render('main', ['users' => $users, 'model' => $model]);
    }


}
