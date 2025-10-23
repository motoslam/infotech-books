<?php

namespace app\components;

use yii\base\Component;

class SmsService extends Component
{
    private $apiKey;
    private $baseUrl = 'https://smspilot.ru/api2.php';

    public function __construct($apiKey, $config = [])
    {
        $this->apiKey = $apiKey;
        parent::__construct($config);
    }

    public function send($packMessages): bool
    {
        $send = array(
            'apikey' => $this->apiKey,
            'from' => 'INFOTECH',
            'send' => $packMessages
        );

        $result = file_get_contents($this->baseUrl, false, stream_context_create(array(
            'http' => array(
                'method' => 'POST',
                'header' => "Content-Type: application/json\r\n",
                'content' => json_encode( $send ),
            ),
        )));

        // Тут просто проверка нет ли ошибок
        // На "боевом" проекте стоит добавить логирование отправки,
        // более подробную проверку ответа и т.д.
        // Декодируем JSON ответ
        $response = json_decode($result);

        if (json_last_error() === JSON_ERROR_NONE) {
            return !isset($response->error);
        } else {
            return false;
        }
    }
}