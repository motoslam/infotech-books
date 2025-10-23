<?php

namespace app\controllers;

use app\models\User;
use app\models\UsersAuthorsSubscription;
use Yii;
use app\models\Author;
use yii\data\ActiveDataProvider;
use yii\db\StaleObjectException;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

class AuthorController extends BaseController
{

    /**
     * @throws \Throwable
     */
    public function actionIndex(): string
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Author::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'user' => $this->user,
        ]);
    }

    /**
     * Displays a single Author model.
     * @param int $id ID
     * @return string
     * @throws \Throwable
     */
    public function actionView(int $id): string
    {
        $isSubscribed = false;
        if (!Yii::$app->user->isGuest) {
            $isSubscribed = UsersAuthorsSubscription::find()
                ->where([
                    'users_id' => Yii::$app->user->id,
                    'authors_id' => $id
                ])
                ->exists();
        }

        return $this->render('view', [
            'model' => $this->findModel($id),
            'user' => $this->user,
            'isSubscribed' => $isSubscribed,
        ]);
    }

    /**
     * Creates a new Author model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate(): \yii\web\Response|string
    {
        $model = new Author();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Author model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate(int $id): \yii\web\Response|string
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Author model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete(int $id): \yii\web\Response
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public function actionSubscribe($id): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (Yii::$app->user->isGuest) {
            return [
                'success' => false,
                'message' => 'Необходимо авторизоваться'
            ];
        }

        $author = Author::findOne($id);
        if (!$author) {
            return [
                'success' => false,
                'message' => 'Автор не найден'
            ];
        }

        $userId = Yii::$app->user->id;

        $subscription = UsersAuthorsSubscription::findOne([
            'users_id' => $userId,
            'authors_id' => $id
        ]);

        try {
            if ($subscription) {
                // Отписываемся
                $subscription->delete();

                return [
                    'success' => true,
                    'subscribed' => false,
                    'message' => 'Вы отписались от автора'
                ];
            } else {
                // Подписываемся
                $newSubscription = new UsersAuthorsSubscription();
                $newSubscription->users_id = $userId;
                $newSubscription->authors_id = $id;

                if ($newSubscription->save()) {
                    return [
                        'success' => true,
                        'subscribed' => true,
                        'message' => 'Вы подписались на автора'
                    ];
                }
            }
        } catch (\Exception $e) {
            Yii::error('Ошибка подписки: ' . $e->getMessage());
        }

        return [
            'success' => false,
            'message' => 'Произошла ошибка при изменении подписки'
        ];
    }

    /**
     * Finds the Author model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Author the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Author::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
