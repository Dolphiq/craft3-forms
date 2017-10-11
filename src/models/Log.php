<?php
/**
 * Created by PhpStorm.
 * User: lucasweijers
 * Date: 09-06-17
 * Time: 15:07
 */

namespace plugins\dolphiq\form\models;

/**
 * Class logModel
 * @package plugins\dolphiq\form\models
 * @property int $id
 * @property string $form_data
 * @property string $server_data
 * @property string $html_mail
 * @property string $created_at
 */
class log extends \yii\db\ActiveRecord {

    /**
     * @return string the name of the table associated with this ActiveRecord class.
     */
    public static function tableName()
    {
        return '{{dq_form_log}}';
    }

    public function rules()
    {
        return [
            ['form_data', 'server_data', 'html_mail', 'required']
        ];
    }
}