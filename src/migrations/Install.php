<?php
/**
 * Created by PhpStorm.
 * User: lucasweijers
 * Date: 03-10-17
 * Time: 13:14
 */

namespace plugins\dolphiq\form\migrations;

use craft\db\Migration;
use yii\db\Schema;

class Install extends Migration
{
    public function safeUp()
    {
        $this->createTable('dq_form_log', [
            'id' => Schema::TYPE_PK,
            'form_data' => Schema::TYPE_TEXT,
            'server_data' => Schema::TYPE_TEXT,
            'html_mail' => Schema::TYPE_TEXT,
            'dateCreated' => Schema::TYPE_DATETIME,
            'dateUpdated' => Schema::TYPE_DATETIME,
            'uid' => Schema::TYPE_INTEGER,
        ]);
    }

    public function safeDown()
    {
        $this->dropTableIfExists('dq_form_log');
    }
}