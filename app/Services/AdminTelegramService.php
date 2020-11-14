<?php


namespace App\Services;


use App\Models\Subject;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Laravel\Facades\Telegram;

class AdminTelegramService
{
    protected $telegramUserService;
    protected $regularService;
    protected $subjectService;
    public function __construct(TelegramUserService $telegramUserService, RegularService $regularService, SubjectService $subjectService)
    {
        $this->telegramUserService = $telegramUserService;
        $this->regularService = $regularService;
        $this->subjectService = $subjectService;
    }

    public function sendHello($user)
    {
        $name = $user->first_name != '@' ? $user->first_name : '';

        $text = "👋Salom " . $name .  " <b>! \n \n 🏢\"ABACUS\"</b> o'quv markazi botiga xush kelibsiz!!!";


        Telegram::sendMessage(['chat_id' =>$user->chat_id, 'parse_mode'=>'html','text' => $text]);
        $this->sendHomeMarkup($user);

    }
    public function sendHomeMarkup($user)
    {
        $text ="Kerakli bo'limni tanlang👇";

        $keyboard = [
            ['📝 Yangi test joylashtirish', "📊 Test natijalarini ko'rish"],
            ["👨‍👩‍👧‍👦 Bot foydalanuvchilarini ko'rish", "📚 Fan qo'shish"]
        ];
        $reply_markup = Keyboard::make([
            'keyboard' => $keyboard,
            'resize_keyboard' => true,
            'one_time_keyboard' => true
        ]);

        $this->telegramUserService->setUserStep($user, 1);

        Telegram::sendMessage(['chat_id' =>$user->chat_id, 'parse_mode'=>'html','text' => $text, 'reply_markup' => $reply_markup]);
    }

    public function selectCategory($user, $data)
    {
//        Log::info($this->regularService->checkSelectCategory($data, $user));
        if ($this->regularService->checkSelectCategory($data, $user)) {
            if ($data['message']['text'] == "📚 Fan qo'shish") {
                $this->selectSubjectAdd($user);
            }

        } else {
            $this->sendHomeMarkup($user);
        }
    }
    public function selectSubjectAdd($user)
    {
        $text = "Fan nomini kiriting:";
        $keyboard = [
            ['🔙 Asosiy menyuga']
        ];
        $reply_markup = Keyboard::make([
            'keyboard' => $keyboard,
            'resize_keyboard' => true,
            'one_time_keyboard' => true
        ]);
        Telegram::sendMessage(['chat_id' =>$user->chat_id, 'parse_mode'=>'html','text' => $text, 'reply_markup' => $reply_markup]);
        $this->telegramUserService->setUserStep($user, 2);
    }

    public function getSubjectName($user, $data)
    {
        if($this->regularService->checkSubjectName($data, $user)) {
            $subject = $this->subjectService->create($data['message']['name']);
            if($subject) {
                $text = " <b>". $subject->name ."</b>  fani muvaffaqiyatli qo'shildi🎉";

                Telegram::sendMessage(['chat_id' =>$user->chat_id, 'parse_mode'=>'html','text' => $text]);
                $this->telegramUserService->setUserStep($user, 1);
                $this->sendHomeMarkup($user);
            }
        }
    }


}
