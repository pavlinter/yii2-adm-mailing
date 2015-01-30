<?php

use yii\db\Schema;
use yii\db\Migration;

class m150127_115058_mailing extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%mailing}}', [
            'id' => Schema::TYPE_PK,
            'def_language_id' => Schema::TYPE_INTEGER . " NULL",
            'title' => Schema::TYPE_STRING . "(250) NULL",
            'email' => Schema::TYPE_STRING . "(320) NULL",
            'name' => Schema::TYPE_STRING . "(250) NULL",
            'reply_email' => Schema::TYPE_STRING . "(320) NULL",
            'reply_name' => Schema::TYPE_STRING . "(250) NULL",
            'type' => Schema::TYPE_STRING . "(50) NOT NULL",
            'created_at' => Schema::TYPE_TIMESTAMP . " NOT NULL",
            'updated_at' => Schema::TYPE_TIMESTAMP . " NOT NULL",
        ], $tableOptions);


        $this->createTable('{{%mailing_lang}}', [
            'id' => Schema::TYPE_PK,
            'mailing_id' => Schema::TYPE_INTEGER . " NOT NULL",
            'language_id' => Schema::TYPE_INTEGER . " NOT NULL",
            'subject' => Schema::TYPE_STRING . "(100)",
            'text' => Schema::TYPE_TEXT,
        ], $tableOptions);

        $this->createIndex('page_id', '{{%mailing_lang}}', 'mailing_id');
        $this->createIndex('language_id', '{{%mailing_lang}}', 'language_id');

        $this->addForeignKey('mailing_lang_ibfk_1', '{{%mailing_lang}}', 'mailing_id', '{{%mailing}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('mailing_lang_ibfk_2', '{{%mailing_lang}}', 'language_id', '{{%language}}', 'id');

    }

    public function down()
    {
        $this->dropTable('{{%page_lang}}');
        $this->dropTable('{{%page}}');
    }
}
