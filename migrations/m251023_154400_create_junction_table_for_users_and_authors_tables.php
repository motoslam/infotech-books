<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%users_authors}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%users}}`
 * - `{{%authors}}`
 */
class m251023_154400_create_junction_table_for_users_and_authors_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%users_authors_subscription}}', [
            'users_id' => $this->integer(),
            'authors_id' => $this->integer(),
            'PRIMARY KEY(users_id, authors_id)',
        ]);

        // Добавляем уникальный индекс для предотвращения дублирования подписок
        $this->createIndex(
            'idx-users_authors_subscription-users_id-authors_id',
            '{{%users_authors_subscription}}',
            ['users_id', 'authors_id'],
            true
        );

        // creates index for column `users_id`
        $this->createIndex(
            '{{%idx-users_authors_subscription-users_id}}',
            '{{%users_authors_subscription}}',
            'users_id'
        );

        // add foreign key for table `{{%users}}`
        $this->addForeignKey(
            '{{%fk-users_authors_subscription-users_id}}',
            '{{%users_authors_subscription}}',
            'users_id',
            '{{%users}}',
            'id',
            'CASCADE'
        );

        // creates index for column `authors_id`
        $this->createIndex(
            '{{%idx-users_authors_subscription-authors_id}}',
            '{{%users_authors_subscription}}',
            'authors_id'
        );

        // add foreign key for table `{{%authors}}`
        $this->addForeignKey(
            '{{%fk-users_authors_subscription-authors_id}}',
            '{{%users_authors_subscription}}',
            'authors_id',
            '{{%authors}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `{{%users}}`
        $this->dropForeignKey(
            '{{%fk-users_authors_subscription-users_id}}',
            '{{%users_authors_subscription}}'
        );

        // drops index for column `users_id`
        $this->dropIndex(
            '{{%idx-users_authors_subscription-users_id}}',
            '{{%users_authors_subscription}}'
        );

        // drops foreign key for table `{{%authors}}`
        $this->dropForeignKey(
            '{{%fk-users_authors_subscription-authors_id}}',
            '{{%users_authors_subscription}}'
        );

        // drops index for column `authors_id`
        $this->dropIndex(
            '{{%idx-users_authors_subscription-authors_id}}',
            '{{%users_authors_subscription}}'
        );

        // drops unique index
        $this->dropIndex(
            '{{%idx-users_authors_subscription-users_id-authors_id}}',
            '{{%users_authors_subscription}}'
        );

        $this->dropTable('{{%users_authors_subscription}}');
    }
}
