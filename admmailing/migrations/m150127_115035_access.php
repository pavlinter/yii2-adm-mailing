<?php

use yii\db\Schema;
use yii\db\Migration;

class m150127_115035_access extends Migration
{
    public function up()
    {
        $this->batchInsert('{{%auth_item}}', ['name', 'type', 'description', 'rule_name', 'data', 'created_at', 'updated_at'],[
            [
                'Adm-Mailing',
                2,
                'Access to Mailing module',
                NULL,
                NULL,
                time(),
                time(),
            ],
        ]);

        $this->batchInsert('{{%auth_item_child}}', ['parent', 'child'],[
            [
                'AdmRoot',
                'Adm-Mailing',
            ],
        ]);
    }

    public function down()
    {
        $this->delete('{{auth_item_child}}', "parent='AdmRoot' AND child='Adm-Mailing'");
        $this->delete('{{auth_item}}', "name='Adm-Mailing'");
    }
}
