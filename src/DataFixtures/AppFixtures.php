<?php

namespace App\DataFixtures;

use App\Entity\Admin;
use App\Entity\Transaction;
use App\Entity\User;
use App\Repository\AdminRepository;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $passwordEncoder;
    /**
     * @var Generator
     */
    private $faker;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder) {
        $this->passwordEncoder = $passwordEncoder;
        $this->faker = Factory::create('fr_CH');
    }

    public function load(ObjectManager $manager)
    {
        $users = [
            ["Gil", "Balsiger", "gil.balsiger@heig-vd.ch"],
            ["Julien", "Béguin", "julien.beguin@heig-vd.ch"],
            ["Thomas", "Rieder", "thomas.rieder@heig-vd.ch"],
            ["Nicolas", "Müller", "nicolas.muller1@heig-vd.ch"],
            ["Denis", "Bourqui", "denis.bourqui@heig-vd.ch"],
            ["Matthias", "Maillard", "matthis.maillard@heig-vd.ch"],
        ];

        $adminInstances = [];

        foreach ($users as $userdata) {
            $user = new User();
            $user->setFirstname($userdata[0]);
            $user->setLastname($userdata[1]);
            $user->setStatus('ACTIVE');
            $user->setTagRfid((string)$this->faker->randomNumber(6));
            $admin = new Admin();
            $admin->setUser($user);
            $admin->setEmail($userdata[2]);
            $admin->setPassword($this->passwordEncoder->encodePassword(
                $admin,
                '1234'
            ));

            $adminInstances[] = $admin;

            $manager->persist($user);
            $manager->persist($admin);
        }

        $userInstances = [];
        for($i = 0; $i < 100; $i++) {
            $user = new User();
            $status = $this->faker->randomElement(['NEW', 'ACTIVE', 'BLOCKED']);
            $user->setFirstname($status != 'NEW' ? $this->faker->firstName : '');
            $user->setLastname($status != 'NEW' ? $this->faker->lastName : '');
            $user->setCreatedAt($this->faker->dateTimeThisMonth);
            $user->setStatus($status);
            $user->setTagRfid((string)$this->faker->randomNumber(6));
            $userInstances[] = $user;
            $manager->persist($user);
        }

        for($i = 0; $i < 500; $i++) {
            $transaction = new Transaction();
            $adminIndex = rand(0, count($adminInstances) - 1);
            $userIndex = rand(0, count($userInstances) - 1);
            $transaction->setAdmin($adminInstances[$adminIndex]);
            $transaction->setUser($userInstances[$userIndex]);
            $transaction->setAmount(rand(-20, 30));
            $transaction->setNumTerminal(rand(1, 2));
            $transaction->setDate($this->faker->dateTimeThisMonth);

            $manager->persist($transaction);
        }

        $manager->flush();
    }
}
