<?php

namespace app\components;

use app\models\Author;
use app\models\UsersAuthorsSubscription;
use Yii;
use yii\web\NotFoundHttpException;

class AuthorSubscriptionService
{
    /**
     * Подписаться или отписаться от автора.
     *
     * @param int $authorId ID автора
     * @param int $userId ID пользователя
     * @return array ['success' => bool, 'subscribed' => bool, 'message' => string]
     */
    public function toggleSubscription(int $authorId, int $userId): array
    {
        $author = Author::findOne($authorId);
        if (!$author) {
            return [
                'success' => false,
                'message' => 'Автор не найден'
            ];
        }

        $subscription = UsersAuthorsSubscription::findOne([
            'users_id' => $userId,
            'authors_id' => $authorId
        ]);

        try {
            if ($subscription) {
                $subscription->delete();
                return [
                    'success' => true,
                    'subscribed' => false,
                    'message' => 'Вы отписались от автора'
                ];
            } else {
                $newSubscription = new UsersAuthorsSubscription();
                $newSubscription->users_id = $userId;
                $newSubscription->authors_id = $authorId;

                if ($newSubscription->save()) {
                    return [
                        'success' => true,
                        'subscribed' => true,
                        'message' => 'Вы подписались на автора'
                    ];
                } else {
                    return [
                        'success' => false,
                        'message' => 'Ошибка при сохранении подписки: ' . json_encode($newSubscription->errors)
                    ];
                }
            }
        } catch (\Exception $e) {
            Yii::error('Ошибка подписки: ' . $e->getMessage(), 'subscription');
            return [
                'success' => false,
                'message' => 'Произошла ошибка при изменении подписки'
            ];
        }
    }

    /**
     * Проверить, подписан ли пользователь на автора.
     *
     * @param int $authorId ID автора
     * @param int|null $userId ID пользователя (если null, возвращается false)
     * @return bool
     */
    public function isSubscribed(int $authorId, ?int $userId): bool
    {
        if ($userId === null) {
            return false;
        }

        return UsersAuthorsSubscription::find()
            ->where([
                'users_id' => $userId,
                'authors_id' => $authorId
            ])
            ->exists();
    }
}