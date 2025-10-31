<?php

namespace app\models;

use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;
use yii\db\Exception;

/**
 * This is the model class for table "books".
 *
 * @property int $id
 * @property string $title
 * @property string|null $year
 * @property string|null $description
 * @property string|null $isbn
 * @property string|null $photo
 *
 * @property Author[] $authors
 */
class Book extends \yii\db\ActiveRecord
{
    public $authorIds = [];

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'books';
    }

    public function init(): void
    {
        parent::init();
        $this->on(self::EVENT_AFTER_INSERT, [$this, 'sendNotifications']);
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['year', 'description', 'isbn', 'photo'], 'default', 'value' => null],
            [['title'], 'required'],
            [['description'], 'string'],
            [['title', 'photo'], 'string', 'max' => 255],
            [['year'], 'string', 'max' => 4],
            [['isbn'], 'string', 'max' => 13],
            [['authorIds'], 'each', 'rule' => ['integer']],
            [['authorIds'], 'required', 'message' => 'Необходимо выбрать хотя бы одного автора'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'title' => 'Название',
            'year' => 'Год',
            'description' => 'Описание',
            'isbn' => 'ISBN',
            'photo' => 'Обложка',
            'authorIds' => 'Авторы',
        ];
    }

    /**
     * Gets query for [[Author]].
     *
     * @return ActiveQuery
     * @throws InvalidConfigException
     */
    public function getAuthors(): ActiveQuery
    {
        return $this->hasMany(Author::class, ['id' => 'authors_id'])
            ->viaTable('authors_books', ['books_id' => 'id']);
    }

    /**
     * Получает список годов, за которые есть книги
     * @return array
     */
    public static function getAvailableYears(): array
    {
        $years = self::find()
            ->select('year')
            ->distinct()
            ->orderBy(['year' => SORT_DESC])
            ->column();

        return $years ?: [date('Y')];
    }

    /**
     * @throws InvalidConfigException
     */
    public function sendNotifications($event): void
    {
        $authors = $this->authors;

        foreach ($authors as $author) {
            $author->notifyAuthorSubscribers($this);
        }
    }

    /**
     * @throws Exception
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if (property_exists($this, 'authorIds') && is_array($this->authorIds)) {
            $currentAuthorIdsQuery = AuthorBook::find()
                ->select('authors_id')
                ->where(['books_id' => $this->id]);

            $currentAuthorIds = array_map('intval', $currentAuthorIdsQuery->column());
            $newAuthorIds = array_map('intval', $this->authorIds);

            $idsToDelete = array_diff($currentAuthorIds, $newAuthorIds);
            $idsToInsert = array_diff($newAuthorIds, $currentAuthorIds);

            if (!empty($idsToDelete)) {
                AuthorBook::deleteAll([
                    'and',
                    ['books_id' => $this->id],
                    ['in', 'authors_id', $idsToDelete]
                ]);
            }

            if (!empty($idsToInsert)) {
                $rows = [];
                foreach ($idsToInsert as $authorId) {
                    if (is_numeric($authorId)) {
                        $rows[] = [$this->id, $authorId];
                    }
                }
                if (!empty($rows)) {
                    \Yii::$app->db->createCommand()
                        ->batchInsert(
                            AuthorBook::tableName(),
                            ['books_id', 'authors_id'], $rows
                        )->execute();
                }
            }
        }
    }

    /**
     * @throws InvalidConfigException
     */
    public function afterFind()
    {
        parent::afterFind();

        // Заполняем authorIds текущими авторами
        $this->authorIds = \yii\helpers\ArrayHelper::getColumn(
            $this->getAuthors()->select('id')->asArray()->all(),
            'id'
        );
    }

}
