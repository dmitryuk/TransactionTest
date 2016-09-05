<?php
/* @var $this yii\web\View */
use yii\bootstrap\ActiveForm;

/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\TransactionForm */

?>

<h1>Перечисление средств другому пользователю</h1>

<?php
    $this->title = 'Перечисление средств другому пользователю';
    $form = ActiveForm::begin(
        ['id'=>'send_form']
    );
    echo \yii\helpers\Html::error($model,'error');
    $items = \yii\helpers\ArrayHelper::map($users,'id','name');
    echo $form->field($model,'id_sender')->dropDownList($items,['style'=>'width:auto']);
    echo $form->field($model,'id_requester')->dropDownList($items,['style'=>'width:auto']);
    echo $form->field($model,'sum')->textInput(['style'=>'width:auto']);
    echo \yii\helpers\Html::submitButton('Отправить',['Class'=>'btn btn-success']);
    ActiveForm::end();
?>