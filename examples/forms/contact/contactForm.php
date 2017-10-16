<?php

namespace app\forms;

use Craft;
use plugins\dolphiq\form\models\Form;

class contactForm extends Form {

    public $firstname = "";
    public $lastname = "";
    public $phone = "";
    public $email = "";
    public $message = "";

    public function rules()
    {
        return [
            [['firstname', 'lastname', 'email', 'message'], 'required'],
            ['email', 'email'],
            ['phone', 'safe']
        ];
    }

    public function attributeLabels()
    {
        return [
            'firstname' => Craft::t('site', 'Firstname'),
            'lastname' => Craft::t('site', 'Lastname'),
            'phone' => Craft::t('site', 'Phone'),
            'email' => Craft::t('site', 'Email'),
            'message' => Craft::t('site', 'Message'),
        ];
    }
}
