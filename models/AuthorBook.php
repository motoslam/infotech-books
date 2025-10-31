<?php

namespace app\models;

use yii\db\ActiveRecord;

class AuthorBook extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%authors_books}}';
    }

    public function rules(): array
    {
        return [
            [['books_id', 'authors_id'], 'required'],
            [['books_id', 'authors_id'], 'integer'],
        ];
    }
}