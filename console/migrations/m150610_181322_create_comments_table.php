<?php

use yii\db\Schema;
use yii\db\Migration;

class m150610_181322_create_comments_table extends Migration
{
    public function up()
    {
        $this->createTable('{{comments}}', [
            'id' => 'INT(11) UNSIGNED NOT NULL AUTO_INCREMENT',
            'material_type' => Schema::TYPE_SMALLINT . ' NOT NULL',
            'material_id' => Schema::TYPE_BIGINT . ' NOT NULL',
            'text' => Schema::TYPE_TEXT,
            'user_id' => 'INT(11) UNSIGNED NULL',
            'user_name' => Schema::TYPE_STRING,
            'user_email' => Schema::TYPE_STRING,
            'user_ip' => 'INT(11) UNSIGNED NULL',
            'parent_id' => 'INT(11) UNSIGNED NULL',
            'language_id' => Schema::TYPE_SMALLINT,
            'is_replied' => Schema::TYPE_BOOLEAN . ' NOT NULL DEFAULT 0',
            'is_approved' => Schema::TYPE_BOOLEAN . ' NOT NULL DEFAULT 0',
            'is_deleted' => Schema::TYPE_BOOLEAN . ' NOT NULL DEFAULT 0',
            'created_at' => Schema::TYPE_DATETIME . ' NOT NULL',
            'updated_at' => Schema::TYPE_TIMESTAMP,
            'PRIMARY KEY (`id`)',
            'INDEX `material` (`material_type`, `material_id`)',
            'INDEX `sorting` (`parent_id`, `created_at`)',
            'INDEX `visible` (`is_deleted`)',
        ]);
    }

    public function down()
    {
        $this->dropTable('comments');
    }
}
