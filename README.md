<p align="center">
    <a href="https://github.com/yiisoft" target="_blank">
        <img src="https://avatars0.githubusercontent.com/u/993323" height="100px">
    </a>
    <h1 align="center">infotech-books Yii2</h1>
    <br>
</p>

Тестовое задание на вакансию РНР Developer

Автор: Илья Глаголев (https://t.me/dark_whoami)

Системные требования
------------

Минимальное требование этого проекта - чтобы ваш веб-сервер поддерживал PHP 8.2.


Установка
------------

Клонировать проект из репозитория:

~~~
git clone https://github.com/motoslam/infotech-books.git
~~~

Перейти в папку проекта:

~~~
cd infotech-books
~~~

### Установка через Docker

Обновление зависимостей

    docker-compose run --rm php composer update --prefer-dist

Установка зависимостей

    docker-compose run --rm php composer install    

Старт контейнера

    docker-compose up -d

### Настройка проекта

Запустить миграции и установить начальные данные:

~~~
php yii migrate
php yii seed
~~~

Перейти в браузер

~~~
http://localhost/
~~~
