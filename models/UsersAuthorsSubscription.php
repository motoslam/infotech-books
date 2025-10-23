<?php

namespace app\models;

use yii\db\ActiveRecord;

class UsersAuthorsSubscription extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%users_authors_subscription}}';
    }

    public function rules(): array
    {
        return [
            [['users_id', 'authors_id'], 'required'],
            [['users_id', 'authors_id'], 'integer'],
        ];
    }
}