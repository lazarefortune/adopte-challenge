<?php

namespace App\DataFixtures;

use App\Entity\SubscriptionType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class SubscriptionTypeFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $plans = [
            [
                'name' => 'Basic',
                'price' => 9.99,
                'interval' => 30,
                'commitment' => 1,
                'autoRenew' => true,
            ],
            [
                'name' => 'Premium',
                'price' => 14.99,
                'interval' => 30,
                'commitment' => 3,
                'autoRenew' => true,
            ],
            [
                'name' => 'Ultimate',
                'price' => 19.99,
                'interval' => 45,
                'commitment' => 6,
                'autoRenew' => false,
            ],
        ];

        foreach ($plans as $p) {
            $type = new SubscriptionType();
            $type->setName($p['name']);
            $type->setPrice($p['price']);
            $type->setBillingIntervalDays($p['interval']);
            $type->setCommitmentMonths($p['commitment']);
            $type->setAutoRenew($p['autoRenew']);

            $manager->persist($type);
        }


        $manager->flush();
    }
}
