<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $hasher) {}

    public function load(ObjectManager $manager): void
    {
        // Root user
        $root = new User();
        $root->setLogin('root');
        $root->setPhone('+380991111111');
        $root->setPassword($this->hasher->hashPassword($root, 'rootpass'));
        $root->setRoles(['ROLE_ROOT']);
        $manager->persist($root);

        // Regular user
        $user = new User();
        $user->setLogin('user1');
        $user->setPhone('+380992222222');
        $user->setPassword($this->hasher->hashPassword($user, 'userpass'));
        $user->setRoles(['ROLE_USER']);
        $manager->persist($user);

        $manager->flush();
    }
}
