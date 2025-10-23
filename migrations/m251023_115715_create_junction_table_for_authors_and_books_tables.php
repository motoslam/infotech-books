<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%authors_books}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%authors}}`
 * - `{{%books}}`
 */
class m251023_115715_create_junction_table_for_authors_and_books_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%authors_books}}', [
            'authors_id' => $this->integer(),
            'books_id' => $this->integer(),
            'PRIMARY KEY(authors_id, books_id)',
        ]);

        // creates index for column `authors_id`
        $this->createIndex(
            '{{%idx-authors_books-authors_id}}',
            '{{%authors_books}}',
            'authors_id'
        );

        // add foreign key for table `{{%authors}}`
        $this->addForeignKey(
            '{{%fk-authors_books-authors_id}}',
            '{{%authors_books}}',
            'authors_id',
            '{{%authors}}',
            'id',
            'CASCADE'
        );

        // creates index for column `books_id`
        $this->createIndex(
            '{{%idx-authors_books-books_id}}',
            '{{%authors_books}}',
            'books_id'
        );

        // add foreign key for table `{{%books}}`
        $this->addForeignKey(
            '{{%fk-authors_books-books_id}}',
            '{{%authors_books}}',
            'books_id',
            '{{%books}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `{{%authors}}`
        $this->dropForeignKey(
            '{{%fk-authors_books-authors_id}}',
            '{{%authors_books}}'
        );

        // drops index for column `authors_id`
        $this->dropIndex(
            '{{%idx-authors_books-authors_id}}',
            '{{%authors_books}}'
        );

        // drops foreign key for table `{{%books}}`
        $this->dropForeignKey(
            '{{%fk-authors_books-books_id}}',
            '{{%authors_books}}'
        );

        // drops index for column `books_id`
        $this->dropIndex(
            '{{%idx-authors_books-books_id}}',
            '{{%authors_books}}'
        );

        $this->dropTable('{{%authors_books}}');
    }
}
