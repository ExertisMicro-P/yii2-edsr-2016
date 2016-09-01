<?php
Yii::setAlias('common', dirname(__DIR__));
Yii::setAlias('frontend', dirname(dirname(__DIR__)) . '/frontend');
Yii::setAlias('backend', dirname(dirname(__DIR__)) . '/backend');
Yii::setAlias('console', dirname(dirname(__DIR__)) . '/console');

Yii::$classMap['GAuthifyError'] = '@vendor/gauthify/gauthify.php';
Yii::$classMap['ApiKeyError'] = '@vendor/gauthify/gauthify.php';
Yii::$classMap['ParameterError'] = '@vendor/gauthify/gauthify.php';
Yii::$classMap['NotFoundError'] = '@vendor/gauthify/gauthify.php';
Yii::$classMap['ServerError'] = '@vendor/gauthify/gauthify.php';
Yii::$classMap['RateLimitError'] = '@vendor/gauthify/gauthify.php';
Yii::$classMap['ConflictError'] = '@vendor/gauthify/gauthify.php';
Yii::$classMap['GAuthify'] = '@vendor/gauthify/gauthify.php';

//Yii::$classMap['Edvlerblog\Ldap'] = '@vendor/yii2-adldap-module/src/Ldap.php';
