<?php

namespace app\commands;

use app\models\Book;
use Yii;
use app\models\Author;
use yii\base\Exception;
use yii\console\Controller;
use yii\base\Security;
use Faker\Factory;
use Faker\Generator;
use app\models\User;

class SeedController extends Controller
{
    private Generator $faker;
    private array $authorIds = [];

    public function __construct($id, $module, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->faker = Factory::create();
    }

    /**
     * @throws \yii\db\Exception
     * @throws Exception
     */
    public function actionIndex(): void
    {
        $this->seedUsers();
        $this->seedAuthors();
        $this->seedBooks();

        echo "All seeding completed successfully!\n";
    }

    /**
     * @throws Exception
     * @throws \yii\db\Exception
     */
    private function seedUsers(): void
    {
        $users = [
            [
                'username' => 'admin',
                'role' => 100,
            ],
            [
                'username' => 'guest',
                'role' => 10,
            ]
        ];

        foreach ($users as $userData) {
            if (User::find()->where(['username' => $userData['username']])->exists()) {
                continue;
            }

            $user = new User();
            $user->username = $userData['username'];
            $user->email = $this->faker->email;
            $user->phone = $this->faker->phoneNumber();
            $user->role = $userData['role'];
            $user->setPassword($userData['username']);
            $user->generateAuthKey();

            if ($user->save()) {
                echo "User '{$userData['username']}' created\n";
            }
        }

        echo "---\n";
    }

    /**
     * @throws \yii\db\Exception
     */
    private function seedAuthors(): void
    {
        $authors = [
            'Александр Пушкин',
            'Лев Толстой',
            'Фёдор Достоевский',
            'Николай Гоголь',
            'Иван Тургенев',
            'Михаил Лермонтов',
            'Александр Островский',
        ];

        $authorRows = array_map(fn($name) => ['name' => $name], $authors);

        Yii::$app->db->createCommand()->delete(Author::tableName())->execute();
        Yii::$app->db->createCommand()
            ->batchInsert(Author::tableName(), ['name'], $authorRows)
            ->execute();

        $this->authorIds = Author::find()->select('id')->column();

        echo count($authors) . " authors created\n";
    }

    /**
     * @throws \yii\db\Exception
     */
    private function seedBooks(): void
    {
        $bookCount = $this->faker->numberBetween(10, 99);

        Yii::$app->db->createCommand()->delete(Book::tableName())->execute();

        Yii::$app->db->createCommand()->delete('{{%authors_books}}')->execute();

        for ($i = 0; $i < $bookCount; $i++) {
            $book = new Book();
            $book->setAttributes([
                'title' => $this->faker->jobTitle,
                'year' => $this->faker->year,
                'description' => $this->faker->text,
                'isbn' => $this->faker->isbn13(),
                'photo' => $this->faker->imageUrl(640, 480, 'books'),
            ]);

            if ($book->save(false)) {
                $this->linkAuthorsToBook($book);
            }
        }

        echo "{$bookCount} books created\n";
    }

    /**
     * @throws \yii\db\Exception
     */
    private function linkAuthorsToBook(Book $book): void
    {
        // Случайное количество авторов для книги (1-3 автора)
        $authorCount = $this->faker->numberBetween(1, min(3, count($this->authorIds)));
        $randomAuthorIds = $this->faker->randomElements($this->authorIds, $authorCount);

        foreach ($randomAuthorIds as $authorId) {
            Yii::$app->db->createCommand()->insert('{{%authors_books}}', [
                'books_id' => $book->id,
                'authors_id' => $authorId,
            ])->execute();
        }

        echo "Book '{$book->title}' linked to " . count($randomAuthorIds) . " author(s)\n";
    }
}