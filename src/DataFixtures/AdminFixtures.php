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

        $users = [
            ["Gil", "Balsiger", "gil.balsiger@heig-vd.ch"],
            ["Julien", "Béguin", "julien.beguin@heig-vd.ch"],
            ["Thomas", "Rieder", "thomas.rieder@heig-vd.ch"],
            ["Nicolas", "Müller", "nicolas.muller1@heig-vd.ch"],
            ["Denis", "Bourqui", "denis.bourqui@heig-vd.ch"],
            ["Matthias", "Maillard", "matthis.maillard@heig-vd.ch"],
        ];

        foreach ($users as $userdata) {
            $user = new User();
            $user->setFirstname($userdata[0]);
            $user->setLastname($userdata[1]);
            $user->setStatus("actif");
            $user->setTagRfid(uniqid());
            $admin = new Admin();
            $admin->setUser($user);
            $admin->setEmail($userdata[2]);
            $admin->setPassword($this->passwordEncoder->encodePassword(
                $admin,
                '1234'
            ));

            $manager->persist($user);
            $manager->persist($admin);
        }

        $manager->flush();
    }
}
