<?php

namespace App\DataFixtures;

use App\Entity\AccountNumber;
use App\Entity\CardNumber;
use App\Entity\Fee;
use App\Entity\Transaction;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public $hash;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->hash = $passwordHasher;
    }


    public function load(ObjectManager $manager): void
    {
        $cards = [
            '6219861918302125',
            '6037998213617482',
            '6274121196735898'
        ];
        $faker = \Faker\Factory::create('fa_IR');
        for ($x = 1; $x <= 1; $x++) {
            $user = new User();
            $user->setPassword($this->hash->hashPassword($user, 'hesaam@1391'));
            $user->setMobile('09357779306');
            $user->setName('hesam');
            $user->setVerified(0);
            $code = random_int(11111, 99999);
            $user->setVerifyCode($code);
            $manager->persist($user);
            $manager->flush();
        }
        for ($x = 1; $x <= 3; $x++) {
            $accountNumber = new AccountNumber();
            $accountNumber->setUser($user);
            $accountNumber->setBalance(0);
            $accountNumber->setAccountNumber(random_int(1111, 9999));
            $manager->persist($accountNumber);
            $manager->flush();
        }
        for ($x = 0; $x <= 2; $x++) {
            $accountNumbers = $manager->getRepository(AccountNumber::class)->findAll();
            $cardNumber = new CardNumber();
            $cardNumber->setAccountNumber($accountNumbers[array_rand($accountNumbers)]);
            $cardNumber->setCardtNumber($cards[$x]);
            $manager->persist($cardNumber);
            $manager->flush();
        }
        $manager->flush();
        foreach ($manager->getRepository(CardNumber::class)->findAll() as $cardNumber) {
            $initBalance = new Transaction();
            $initBalance->setDescription('واریز مبلغ اولیه');
            $initBalance->setPrice(random_int(100000,50000000));
            $initBalance->setCardNumber($cardNumber);
            $initBalance->setType('1');
            $manager->persist($initBalance);
            $accountNumber = $cardNumber->getAccountNumber();
            $accountNumber->setBalance($accountNumber->getBalance() + $initBalance->getPrice());
            $manager->flush();
        }

        $fee = new Fee();
        $fee->setType('transfer');
        $fee->setPrice(500);
        $manager->persist($fee);
        $manager->flush();


    }
}
