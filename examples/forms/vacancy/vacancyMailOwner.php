<?php

use yii\widgets\DetailView;

/* @var $this \yii\web\View view component instance */
/* @var $message \yii\mail\BaseMessage instance of newly created mail message */
/* @var $model \app\forms\vacancyForm */

?>
<h2>An apply for a vacancy has been received</h2>
<p>
    We received the following details:<br>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'vacancy',
            'firstname',
            'lastname',
            'email',
            'phone',
            'message:ntext'
        ]
    ]); ?>
</p>