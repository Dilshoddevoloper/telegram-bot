<?php

namespace App\Http\Controllers;

use App\Services\TelegramService;
use App\Services\TelegramUserService;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Telegram\Bot\Laravel\Facades\Telegram;

class TelegramController extends Controller
{
    protected $ng_domain;
    protected $access_token;
    protected $userService;
    protected $service;
    public function __construct( TelegramUserService $userService, TelegramService $service)
    {
        $this->ng_domain = env('NG_DOMAIN', null);
        $this->access_token = Telegram::getAccessToken();
        $this->userService = $userService;
        $this->service = $service;
    }

    protected function setWebhook(Request $request)
    {
        $result = $this->sendTelegramData('setwebhook', [
            'query' => [ 'url' => $this->ng_domain . '/' . $this->access_token ]
        ]);
        return $result;
    }

//    protected function getWebhook(Request $request)
//    {
//        $result = $this->sendTelegramData('getwebhookInfo', [
//            'query' => [ 'url' => $this->ng_domain . '/' . $this->access_token ]
//        ]);
//    }
    protected function sendTelegramData($route = '', $params = [], $method = 'POST')
    {
        $client = new Client(['base_uri' => 'https://api.telegram.org/bot' . $this->access_token . '/']);
        $result = $client->request($method, $route, $params);

        return (string) $result->getBody();
    }
    protected function action(Request $request)
    {
//        Log::info($request);
        $user = $this->userService->createIfNotExists($request->all());

        switch ($user->step) {
            case 0: {
                $this->service->sendHello($user);
            }
        }

    }
}
