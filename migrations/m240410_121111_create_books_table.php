<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%book}}`.
 */
class m240410_121111_create_books_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%books}}', [
            'id' => $this->primaryKey(),
            'author_id' => $this->bigInteger()->notNull(),
            'title' => $this->string()->notNull(),
            'pages' => $this->integer()->notNull(),
            'language' => $this->string()->defaultValue('русский'),
            'genre' => $this->string()->notNull(),
            'description' => $this->string()->null()
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%books}}');
    }
}
