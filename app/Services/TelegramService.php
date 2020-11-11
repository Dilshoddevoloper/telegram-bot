<?php


namespace App\Services;


use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Laravel\Facades\Telegram;

class TelegramService
{
    public function sendHello($user) {
     $name = $user->first_name;

     $text = 'Salom ' . (($name != '@') ? $name : '')
     . '! Sizga qanday yordam bera olaman';

        $keyboard = [
            ['📝 Янги тест жойлаштириш', '📊 Тест натижаларини кўриш'],
            ['👨‍👩‍👧‍👦 Bot foydalanuvchilarini ko\'rish']
        ];
        $reply_markup = Keyboard::make([
            'keyboard' => $keyboard,
            'resize_keyboard' => true,
            'one_time_keyboard' => true
        ]);
    Telegram::sendMessage(['chat_id' =>$user->chat_id, 'parse_mode'=>'html','text' => $text, 'reply_markup' => $reply_markup]);
    }
}
