<?php


namespace App\Repositories;


use App\Models\User;
use Illuminate\Support\Facades\Log;

class UserRepository
{
    public function createIfNotExists($data) {
        $user = User::where('chat_id', $data['message']['chat']['id'])->first();

        if(!$user) {
            $user = User::create([
                'chat_id' => $data['message']['chat']['id'],
                'first_name' => $data['message']['chat']['first_name'],
                'last_name' => isset($data['message']['chat']['last_name']) ? $data['message']['chat']['last_name'] : null,
                'language_code' => $data['message']['from']['language_code'],
                'username' => isset($data['message']['chat']['username']) ? $data['message']['chat']['username'] : null
            ]);
        }
        return $user;
    }

    public function setUserStep($user, $step) {
        $user->update([
            'step' => $step ?? $user->step + 1
        ]);
    }

    public function getUsers($page) {
        return User::where('username', '!=', config('telegram.admin_username'))->offset($page * 10)->limit(10)->get();
    }

}
