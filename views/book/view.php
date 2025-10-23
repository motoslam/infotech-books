<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Book $model */
/** @var app\models\User $user */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Книги', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="book-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php if($user and $user->canEdit()): ?>
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
            'title',
            [
                'label' => 'Авторы',
                'value' => function ($model) {
                    $authorLinks = [];
                    foreach ($model->authors as $author) {
                        $authorLinks[] = Html::a(Html::encode($author->name), ['author/view', 'id' => $author->id]);
                    }
                    return $authorLinks ? implode(', ', $authorLinks) : 'Авторы не указаны';
                },
                'format' => 'raw',
            ],
            'year',
            'description:ntext',
            'isbn',
            'photo',
        ],
    ]) ?>

</div>
