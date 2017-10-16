<?php

/**
 * @var $model app\forms\vacancyForm
 * @var $handle string
 */

use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

// Start ajax handling of the form
Pjax::begin(['enablePushState' => false, 'id' => 'pjax-'.$handle]);

// Start active form
$form = ActiveForm::begin([
    'action' => \craft\helpers\UrlHelper::actionUrl('dolphiq-craft3-forms/main/index', ['handle' => $handle]),
    'method' => 'POST',
    'options' => [
        'data-pjax' => true,
    ],
]);

?>

<?= $form->field($model, 'vacancy')->dropDownList($model->vacancyDropdown()); ?>
<?= $form->field($model, 'firstname')->textInput(); ?>
<?= $form->field($model, 'lastname')->textInput(); ?>
<?= $form->field($model, 'phone')->textInput(); ?>
<?= $form->field($model, 'email')->textInput(); ?>

<?= $form->field($model, 'message')->textarea(); ?>

    <div>
        <button type="submit">
            <?= Craft::t('site', 'Apply'); ?>
        </button>
    </div>


<?php

  // End active form
  ActiveForm::end();

  // End ajax handling
  Pjax::end();
