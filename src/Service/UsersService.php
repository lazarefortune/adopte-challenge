<?php

namespace App\Service;

use App\Repository\PurchaseRepository;
use App\Repository\SubscriptionRepository;
use App\Repository\UserRepository;

readonly class UsersService
{
    public function __construct(
        private readonly UserRepository $userRepo,
        private readonly SubscriptionRepository $subRepo,
        private readonly PurchaseRepository $purchaseRepo
    ) {}

    public function getUsersForAdmin(): array
    {
        return $this->userRepo->findAllForAdminList();
    }

    public function getUserDetails(int $userId): ?array
    {
        $user = $this->userRepo->findUserInfo($userId);

        if (!$user) {
            return null;
        }

        $subscriptions = $this->subRepo->findUserSubscriptions($userId);

        $purchases = $this->purchaseRepo->findUserPurchases($userId);

        return [
            'user' => $user,
            'subscriptions' => $subscriptions,
            'purchases' => $purchases,
        ];
    }

}
