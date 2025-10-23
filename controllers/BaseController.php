<?php

namespace app\controllers;

use app\models\User;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;
use yii\web\Controller;

class BaseController extends Controller
{
    protected $user;

    /**
     * @inheritdoc
     * @throws BadRequestHttpException|\Throwable
     */
    public function beforeAction($action)
    {
        if (!parent::beforeAction($action)) {
            return false;
        }
        $this->user = Yii::$app->user->getIdentity();
        return true;
    }

    public function behaviors(): array
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::class,
                    'actions' => [
                        'delete' => ['POST'],
                        'subscribe' => ['POST'],
                    ],
                ],
                'access' => [
                    'class' => AccessControl::class,
                    'only' => ['create', 'update', 'delete'],
                    'rules' => [
                        [
                            'allow' => true,
                            'actions' => ['create', 'update', 'delete'],
                            'matchCallback' => function ($rule, $action) {
                                $user = Yii::$app->user->getIdentity();
                                return !empty($user) && $user->canEdit();
                            }
                        ],
                    ],
                ],
            ]
        );
    }
}