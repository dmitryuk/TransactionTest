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
        if ($model->load(\yii::$app->request->post()) && $model->Validate() && $model->makeTransaction()) {
            return $this->redirect(array('success'));
        }
        return $this->render('main', ['users' => $users, 'model' => $model]);
    }

    public function actionSuccess()
    {
        return $this->render('success');
    }

}
