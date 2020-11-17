<?php


namespace App\Services;


use App\Models\Subject;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\FileUpload\InputFile;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Laravel\Facades\Telegram;

class AdminTelegramService
{
    protected $telegramUserService;
    protected $regularService;
    protected $subjectService;
    protected $testService;
    public function __construct( RegularService $regularService, SubjectService $subjectService, TelegramUserService $telegramUserService, TestService $testService)
    {
        $this->telegramUserService = $telegramUserService;
        $this->regularService = $regularService;
        $this->subjectService = $subjectService;
        $this->testService = $testService;
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
            switch ($data['message']['text']) {
                case "📚 Fan qo'shish": {
                    $this->selectSubjectAdd($user);
                } break;
                case "📝 Yangi test joylashtirish": {
                    $this->selectTestAdd($user);
                } break;
                case "👨‍👩‍👧‍👦 Bot foydalanuvchilarini ko'rish": {
                    $this->selectUsersShow($user ,$data);
                } break;
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
            $subject = $this->subjectService->create($data['message']['text']);
            if($subject) {
                $text = " <b>". $subject->name ."</b>  fani muvaffaqiyatli qo'shildi🎉";

                Telegram::sendMessage(['chat_id' =>$user->chat_id, 'parse_mode'=>'html','text' => $text]);
                $this->telegramUserService->setUserStep($user, 1);
                $this->sendHomeMarkup($user);
            }
        }
    }

    public function selectTestAdd($user)
    {
        $text = "Fanni tanlang:";
        $subjects = $this->subjectService->all()->pluck('name');
        $keyboard = $subjects->chunk(3);
        $keyboard->push(['🔙 Asosiy menyuga']);

        $reply_markup = Keyboard::make([
            'keyboard' => $keyboard,
            'resize_keyboard' => true,
            'one_time_keyboard' => true
        ]);
        Telegram::sendMessage(['chat_id' =>$user->chat_id, 'parse_mode'=>'html','text' => $text, 'reply_markup' => $reply_markup]);
        $this->telegramUserService->setUserStep($user, 3);
    }

    public function selectSubject($user, $data)
    {
        if($this->regularService->checkSelectSubject($data, $user)) {
            if ($data['message']['text'] != '🔙 Asosiy menyuga') {

                $this->testService->create($data['message']['text']);

                $text = "Fan: <b>". $data['message']['text'] . "</b> \n \n \n Test javoblarini (,) bilan kiriting: \n Masalan:  <b>A,B,C,D ...</b>";
                $keyboard = [['❌ Bekor qilish']];

                $reply_markup = Keyboard::make([
                    'keyboard' => $keyboard,
                    'resize_keyboard' => true,
                    'one_time_keyboard' => true
                ]);
                Telegram::sendMessage(['chat_id' =>$user->chat_id, 'parse_mode'=>'html','text' => $text, 'reply_markup' => $reply_markup ]);
                $this->telegramUserService->setUserStep($user, 4);
            } else {
                $this->selectTestAdd($user);
            }
        }
    }
    public function getAnswers($user, $data)
    {
        if ($data['message']['text'] != '❌ Bekor qilish') {
            if($this->regularService->checkAnswers($data, $user)) {

                $test_form = $this->testService->update( 'answers', $data['message']['text']);
                $answers = explode(',', $data['message']['text']);
                $text = "Fan: <b>". $test_form->subject->name . "</b> \n";
                foreach ($answers as $key => $answer) {
                    $text .= "<b>". ($key + 1) .") " . $answer . "</b>\n";
                }
                $text .= "\n \n Test yakunlanish muddatini (kk.oo.yyyy) formatda kiriting: \n Masalan:  <b>01.01.1991</b>";

                Telegram::sendMessage(['chat_id' =>$user->chat_id, 'parse_mode'=>'html','text' => $text]);
                $this->telegramUserService->setUserStep($user, 5);
            } else {
                $text = "❌ Noto'g'ri format \n \n Javoblarini (,) bilan kiriting: \n Masalan  <b>A,B,C,D ...</b>";

                Telegram::sendMessage(['chat_id' =>$user->chat_id, 'parse_mode'=>'html','text' => $text]);
            }
        } else {
            $this->testService->deleteLast();
            $this->sendHomeMarkup($user);
        }
    }
    public function getTestStopDate($user, $data)
    {
        if ($data['message']['text'] != '❌ Bekor qilish') {
            if($this->regularService->checkDate($data, $user)) {

                $test_form = $this->testService->update( 'date_stop', $data['message']['text']);
                $answers = explode(',', $test_form->answers);
                $text = "Fan: <b>". $test_form->subject->name . "</b> \n";
                foreach ($answers as $key => $answer) {
                    $text .= "<b>". ($key + 1) .") " . $answer . "</b>\n";
                }
                $text .= "Test boshlanish muddati: <b>" . Carbon::parse($test_form->date_start)->format('d.m.Y') . "</b>\n";
                $text .= "Test yakunlanish muddati: <b>" . Carbon::parse($test_form->date_stop)->format('d.m.Y') . "</b> ";
                $text .= "\n \n Test faylini rasm ko'rinishida yuklang:";

                Telegram::sendMessage(['chat_id' =>$user->chat_id, 'parse_mode'=>'html','text' => $text ]);
                $this->telegramUserService->setUserStep($user, 6);
            } else {
                $text = "❌ Noto'g'ri format \n \n Test yakunlanish muddatini (kk.oo.yyyy) formatda kiriting: \n Masalan:  <b>01.01.1991</b>";

                Telegram::sendMessage(['chat_id' =>$user->chat_id, 'parse_mode'=>'html','text' => $text]);
            }
        } else {
            $this->testService->deleteLast();
            $this->sendHomeMarkup($user);
        }
    }
    public function getTestFile($user, $data)
    {
        if (isset($data['message']['text']) && $data['message']['text'] == '❌ Bekor qilish') {
            $this->testService->deleteLast();
            $this->sendHomeMarkup($user);
        } else if(isset($data['message']['text']) && $data['message']['text'] == '✅ Saqlash') {
            $test = $this->testService->createTrue();
            $answers = explode(',', $test->answers);
            $text = "🎉 Test muvaffaqiyatli yaratildi \n";
            $text .= "Fan: <b>". $test->subject->name . "</b> \n Javoblar: \n";
            foreach ($answers as $key => $answer) {
                $text .= "<b>". ($key + 1) .") " . $answer . "</b>\n";
            }
            $text .= "Test boshlanish muddati: <b>" . Carbon::parse($test->date_start)->format('d.m.Y') . "</b>\n";
            $text .= "Test yakunlanish muddati: <b>" . Carbon::parse($test->date_stop)->format('d.m.Y') . "</b> ";
            foreach (explode(',', $test->file_path) as $file_id) {
                Telegram::sendPhoto(['chat_id' => $user->chat_id, 'photo' => $file_id ]);
            }

            Telegram::sendMessage(['chat_id' =>$user->chat_id, 'parse_mode'=>'html','text' => $text]);
            $this->sendHomeMarkup($user);
        } else {
            if($this->regularService->checkPhoto($data, $user)) {
                $test_form = $this->testService->storeFile($data['message']['photo']);

                $keyboard = [['✅ Saqlash', '❌ Bekor qilish']];

                $reply_markup = Keyboard::make([
                    'keyboard' => $keyboard,
                    'resize_keyboard' => true,
                    'one_time_keyboard' => true
                ]);
                $text = "✅ Rasm muvaffaqiyatli saqlandi! \n \n Yana rasm yuklang yoki <b> Saqlash</b> tugmasini bosing👇";
                Telegram::sendMessage(['chat_id' =>$user->chat_id, 'parse_mode'=>'html','text' => $text, 'reply_markup' => $reply_markup ]);
            } else {
                $text = "❌ Noto'g'ri format \n \n Test faylini rasm shaklida yuklang!";

                Telegram::sendMessage(['chat_id' =>$user->chat_id, 'parse_mode'=>'html','text' => $text]);
            }
        }
    }
    public function selectUsersShow($user, $data)
    {
        $text = "<b> Foydalanuvchilar:</b> \n";
        $users_text = $this->telegramUserService->getUsers(0);
        $text .= $users_text;
        $keyboard = [
            [[ 'text' => '⏮', 'callback_data' => 'page_0' ], [ 'text' => '❌', 'callback_data' => 'delete' ], [ 'text' => '⏭', 'callback_data' => 'page_2']]
        ];
        $reply_markup = Keyboard::make([
            'inline_keyboard' => $keyboard,
            'resize_keyboard' => true,
            'one_time_keyboard' => true
        ]);
        Telegram::sendMessage(['chat_id' =>$user->chat_id, 'parse_mode'=>'html','text' => $text, 'reply_markup' => $reply_markup]);
        $this->telegramUserService->setUserStep($user, 7);
    }
}
