<?php


namespace App\Services;


use App\Repositories\UserRepository;
use Illuminate\Support\Carbon;

class TelegramUserService
{
    protected $userRepo;
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepo = $userRepository;
    }

    public function createIfNotExists($data) {
        return $this->userRepo->createIfNotExists($data);
    }

    public function setUserStep($user, $step = null) {
        return $this->userRepo->setUserStep($user, $step);
    }
    public function getUsers($page) {

        $users = $this->userRepo->getUsers($page);
        $text = "";
        foreach ($users as $key => $user) {
            $last_name = $user->last_name ?? '-';
            $first_name = $user->first_name ?? '-';
            $username = $user->username ?? '-';
            $last_update = Carbon::parse($user->updated_at)->format('d.m.Y');
            $text .=  $key + 1 .".  $last_name $first_name $username $last_update \n";
        }
        return $text;

    }

    public function getPage($data) {
        return isset($data['callback_query']) ? $data['callback_query']['data']['page']: 0;
    }

    public function setUserData($user, $data) {
        return $this->userRepo->setUserData($user, $data);
    }

}
