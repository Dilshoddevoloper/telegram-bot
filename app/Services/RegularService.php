<?php


namespace App\Services;


use App\Models\Subject;
use App\Models\Test;
use Telegram\Bot\Laravel\Facades\Telegram;

class RegularService
{
    public function checkSelectCategory($data, $user) {
        $result = false;
        if($this->checkText($data)) {
          if(in_array($data['message']['text'], ['📝 Yangi test joylashtirish', "📊 Test natijalarini ko'rish", "👨‍👩‍👧‍👦 Bot foydalanuvchilarini ko'rish", "📚 Fan qo'shish"])) {
              if($data['message']['text'] == "📝 Yangi test joylashtirish") {
                  if(Subject::all()->count() == 0) {
                      $this->sendSubjectEmptyMessage($user);
                  }
              }
              if($data['message']['text'] == "📊 Test natijalarini ko'rish") {
                  if(Test::all()->count() == 0) {
                      $this->sendTestEmptyMessage($user);
                  }
              }
              $result = true;
          }
        }

        return $result;
    }

    public function sendSubjectEmptyMessage($user) {
        $text = "<b>Fan kirtilmagan!</b> Iltimos, fan test joylashtirishdan oldin fan kiriting! ";

        Telegram::sendMessage(['chat_id' =>$user->chat_id, 'parse_mode'=>'html','text' => $text]);
    }

    public function sendTestEmptyMessage($user) {
        $text = "Hali hech kim test topshirmadi!";

        Telegram::sendMessage(['chat_id' =>$user->chat_id, 'parse_mode'=>'html','text' => $text]);
    }

    public function checkIsText($data, $user) {
        if ( $this->checkText($data) ) {
            return true;
        } else {
            $text = "Iltimos uzunligi 255 dan kichik bo'lgan matn kiriting!";
            Telegram::sendMessage(['chat_id' =>$user->chat_id, 'parse_mode'=>'html','text' => $text]);
            return false;
        }
    }
    public function checkText($data) {
        return (isset($data['message']) && isset($data['message']['text']) && strlen($data['message']['text']) < 255);
    }
    public function checkSubjectName($data, $user) {
        $result = false;
        if($this->checkText($data)) {
            if ($data['message']['text'] == '🔙 Asosiy menyuga') {
                (new AdminTelegramService())->sendHomeMarkup($user);
            } else {
                $result = true;
            }
        }
        return $result;
    }
}
