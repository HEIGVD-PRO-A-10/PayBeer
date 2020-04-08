<?php

namespace App\DataFixtures;

use App\Entity\Admin;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AdminFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder) {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager) {
        $user = new User();
        $user->setFirstname("Gil");
        $user->setLastname("Balsiger");
        $user->setStatus("actif");
        $user->setTagRfid("123456");
        $admin = new Admin();
        $admin->setUser($user);
        $admin->setEmail("gil.balsiger@heig-vd.ch");
        $admin->setPassword($this->passwordEncoder->encodePassword(
            $admin,
            '1234'
        ));

        $manager->persist($user);
        $manager->persist($admin);

        $manager->flush();
    }
}
