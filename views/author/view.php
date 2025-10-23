<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Author $model */
/** @var app\models\User $user */
/** @var boolean $isSubscribed */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Авторы', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="author-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php if(!empty($user) and $user->canEdit()): ?>
    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>
    <?php endif; ?>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
        ],
    ]) ?>

    <?php if(!empty($user)): ?>
    <p>
        <button type="button"
                class="btn <?= $isSubscribed ? 'btn-outline-secondary' : 'btn-primary' ?> subscribe-btn"
                data-author-id="<?= $model->id ?>">
            <?= $isSubscribed ? 'Отписаться' : 'Подписаться на автора' ?>
        </button>
    </p>
    <?php else: ?>
    <p>
        <code>Для оформления подписки на автора авторизуйтесь</code>
    </p>
    <?php endif; ?>

    <?php if ($model->books): ?>
        <div class="author-books mt-5">
            <h3>Книги автора (<?= count($model->books) ?>)</h3>
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                    <tr>
                        <th>Название</th>
                        <th>Год</th>
                        <th>ISBN</th>
                        <th>Описание</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($model->books as $book): ?>
                        <tr>
                            <td>
                                <a href="<?= Url::to(['book/view', 'id' => $book->id]); ?>">
                                    <?= Html::encode($book->title) ?>
                                </a>
                            </td>
                            <td><?= Html::encode($book->year) ?></td>
                            <td><?= Html::encode($book->isbn) ?></td>
                            <td><?= Html::encode(mb_substr($book->description, 0, 50)) ?>...</td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-info mt-4">
            У этого автора пока нет книг в каталоге.
        </div>
    <?php endif; ?>

</div>
