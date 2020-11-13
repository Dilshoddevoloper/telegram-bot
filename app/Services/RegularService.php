<?php


namespace App\Services;


use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Laravel\Facades\Telegram;

class RegularService
{
    public function checkSelectCategory($data) {
        if(isset($data['message']) && isset($data['message']['text'])) {
          if(in_array($data['message']['text'], ['📝 Янги тест жойлаштириш', '📊 Тест натижаларини кўриш', '👨‍👩‍👧‍👦 Bot foydalanuvchilarini ko\'rish'])) {
              return true;
          }
        }

        return true;
    }
}
