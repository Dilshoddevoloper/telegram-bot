<?php


namespace App\Services;


use App\Repositories\UserRepository;

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

}
