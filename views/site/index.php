<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var array $availableYears */
/** @var array $topAuthors */
/** @var integer $selectedYear */

$this->title = 'Рейтинг авторов';
?>
<div class="site-index">

    <div class="jumbotron text-center bg-transparent mt-5 mb-5">
        <h1 class="display-4">infotech-books Yii2</h1>

        <p class="lead">тестовое задание на вакансию РНР Developer</p>

    </div>

    <h3>Рейтинг авторов</h3>
    <div class="authors-rating">
        <!-- Форма выбора года -->
        <div class="year-selector" style="margin-bottom: 20px;">
            <?= Html::beginForm([''], 'get') ?>
            <?= Html::dropDownList('year', $selectedYear, array_combine($availableYears, $availableYears), [
                'onchange' => 'this.form.submit()',
                'class' => 'form-select',
                'style' => 'width: 150px; display: inline-block;'
            ]) ?>
            <?= Html::endForm() ?>
        </div>

        <!-- Таблица рейтинга -->
        <?php if ($topAuthors): ?>
            <div class="authors-rating">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Автор</th>
                        <th>Количество книг</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($topAuthors as $index => $author): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td>
                                <?= Html::a($author->name, ['author/view', 'id' => $author->id]) ?>
                            </td>
                            <td>
                                <?= $author->getBooks()
                                    ->andWhere(['year' => $selectedYear])
                                    ->count() ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-info">
                За <?= $selectedYear ?> год книги не найдены.
            </div>
        <?php endif; ?>
    </div>


</div>
