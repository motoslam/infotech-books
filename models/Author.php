<?php

namespace app\models;

use app\components\SmsService;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "authors".
 *
 * @property int $id
 * @property string $name
 */
class Author extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'authors';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'name' => 'ФИО',
        ];
    }

    /**
     * Gets query for [[Author]].
     *
     * @return ActiveQuery
     * @throws InvalidConfigException
     */
    public function getBooks(): ActiveQuery
    {
        return $this->hasMany(Book::class, ['id' => 'books_id'])
            ->viaTable('authors_books', ['authors_id' => 'id'])
            ->orderBy(['year' => SORT_DESC]);
    }

    public static function getTopAuthorsByYear($year = null): array
    {
        if ($year === null) {
            $year = date('Y');
        }

        return self::find()
            ->select([
                'authors.*',
                'COUNT(books.id) as books_count'
            ])
            ->leftJoin('authors_books', 'authors_books.authors_id = authors.id')
            ->leftJoin('books', 'books.id = authors_books.books_id')
            ->andWhere(['books.year' => $year])
            ->groupBy('authors.id')
            ->orderBy(['books_count' => SORT_DESC])
            ->limit(10)
            ->all();
    }

    /**
     * @throws InvalidConfigException
     */
    public function getSubscribedUsers()
    {
        return $this->hasMany(User::class, ['id' => 'users_id'])
            ->viaTable('users_authors_subscription', ['authors_id' => 'id']);
    }

    /**
     * @throws InvalidConfigException
     */
    public function notifyAuthorSubscribers($book): void
    {
        $subscribers = $this->getSubscribedUsers()->all();

        $message = "Вышла новая книга '{$book->title}' автора {$this->name}";

        $packMessages = [];
        foreach ($subscribers as $user) {
            if (!empty($user->phone)) {
                $packMessages[] = ['id' => count($packMessages) + 1, 'to' => $user->phone, 'text' => $message];
            }
        }

        try {
            SmsService::send($packMessages);
        } catch (\Exception $e) {
            Yii::error("Ошибка отправки SMS: " . $e->getMessage());
        }
    }

}
