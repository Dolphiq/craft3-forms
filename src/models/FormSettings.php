<?php
/**
 * Created by PhpStorm.
 * User: lucasweijers
 * Date: 11-10-17
 * Time: 09:08
 */

namespace plugins\dolphiq\form\models;

use craft\base\Model;

class FormSettings extends Model
{
    public $enabled = false;
    public $mail_subject_owner;
    public $mail_subject_customer;
    public $mail_to;

    public function rules()
    {
        return [
            [['mail_to'], 'email'],
            [['enabled', 'mail_subject_owner', 'mail_subject_customer', 'mail_to'], 'safe'],
        ];
    }


}