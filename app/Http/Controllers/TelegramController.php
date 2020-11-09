<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Telegram\Bot\Laravel\Facades\Telegram;

class TelegramController extends Controller
{
    protected function setWebhook(Request $request)
    {
        $result = $this->sendTelegramData('setwebhook', [
            'query' => [ 'url' => 'https://773ea4585a3a.ngrok.io' . '/' . Telegram::getAccessToken() ]
        ]);
    }

    protected function getWebhook(Request $request)
    {
        $result = $this->sendTelegramData('setwebhook', [
            'query' => [ 'url' => 'https://773ea4585a3a.ngrok.io' . '/' . Telegram::getAccessToken() ]
        ]);
    }
    protected function sendTelegramData($route = '', $params = [], $method = 'POST')
    {
        $client = new Client(['base_uri' => 'https://api.telegram.org/bot' . Telegram::getAccessToken() . '/']);
        $result = $client->request($method, $route, $params);

        return (string) $result->getBody();
    }
    protected function action(Request $request)
    {
        Log::info($request['message']);
        Storage::put('public/assets/' . $request['message']['document']['file_name'], $request['message']['document']['thumb']);
        $chat = $request['message']['chat'];
        Telegram::sendMessage(['chat_id' =>$chat['id'], 'parse_mode'=>'html','text' => 'Salomeee']);
//        Telegram::sendFile(['chat_id' =>$chat['id'], 'parse_mode'=>'html','text' => 'Salomeee']);
    }
}
