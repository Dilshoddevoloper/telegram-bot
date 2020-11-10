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
        return $this->userRepo->createIfNotExists($data);
    }

}
