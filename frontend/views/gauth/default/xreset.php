<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->registerJsFile('/js/registration.js', ['depends' => 'yii\web\YiiAsset']);

/**
 * @var yii\web\View $this
 * @var yii\widgets\ActiveForm $form
 * @var amnah\yii2\user\models\User $user
 * @var bool $success
 * @var bool $invalidKey
 */

$this->title = Yii::t('user', 'Reset');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="confirm row">
    <article class="contact">

        <div class="short-top">
            <h2>Account Confirmed : Step 2 of 3</h2>
            <p>Thank you for confirming your email address. You are now ready to create your site passsword.</p>
            <p class="smaller">Once this is complete, we will send you another email with details of the final step, your google authentication signup</p>

        </div>


            <div class="row">
                <div class="col-lg-12">
                    <?php $form = ActiveForm::begin(['id' => 'reset-form', 'layout' => 'horizontal',
                                                     'enableClientValidation' => true,
                                                     'enableAjaxValidation' => false]); ?>

                    <?= Html::HiddenInput('key', $userKey) ?>
                    <?= $form->field($user, 'newPassword')->passwordInput() ?>
                    <?= $form->field($user, 'newPasswordConfirm')->passwordInput() ?>

                    <div class="form-group">
                        <?= Html::submitButton(Yii::t('user', 'Create'), ['class' => 'btn submit']) ?>
                    </div>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>


    </article>
</div>
