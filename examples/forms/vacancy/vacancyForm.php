<?php

namespace app\forms;

use Craft;
use plugins\dolphiq\form\models\Form;

class vacancyForm extends Form {

    public $vacancy = "";
    public $firstname = "";
    public $lastname = "";
    public $phone = "";
    public $email = "";
    public $message = "";

    public function rules()
    {
        return [
            [['vacancy','firstname', 'lastname', 'email', 'message'], 'required'],
            ['email', 'email'],
            ['phone', 'safe']
        ];
    }

    public function attributeLabels()
    {
        return [
            'vacancy' => Craft::t('site', 'Vacancy'),
            'firstname' => Craft::t('site', 'Firstname'),
            'lastname' => Craft::t('site', 'Lastname'),
            'phone' => Craft::t('site', 'Phone'),
            'email' => Craft::t('site', 'Email'),
            'message' => Craft::t('site', 'Message'),
        ];
    }

    public function vacancyDropdown(){
        $vacancies = [
            Craft::t('site', 'Director'),
            Craft::t('site', 'Assistant'),
            Craft::t('site', 'Cleaner'),
            Craft::t('site', 'Intern'),
        ];

        return array_combine($vacancies,$vacancies);
    }
}
