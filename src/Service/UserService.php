<?php

namespace App\Service;

use App\Entity\User;
use App\Enum\UserRole;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Security;

class UserService
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserPasswordHasherInterface $hasher,
        private Security $security,
    ) {}

    public function getAll(): array
    {
        $currentUser = $this->security->getUser();

        if ($this->security->isGranted(UserRole::ROOT->value)) {
            return $this->em->getRepository(User::class)->findAll();
        }

        return $currentUser ? [$currentUser] : [];
    }

    public function getById(int $id): ?User
    {
        return $this->em->getRepository(User::class)->find($id);
    }

    public function create(array $data): User
    {
        $user = new User();
        
        $user->setLogin($data['login']);
        $user->setPhone($data['phone']);
        $user->setPassword(
            $this->hasher->hashPassword($user, $data['pass'])
        );

        $user->setRoles([UserRole::USER->value]);

        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }

    public function update(User $user, array $data): User
    {
        $user->setLogin($data['login']);
        $user->setPhone($data['phone']);
        $user->setPassword(
            $this->hasher->hashPassword($user, $data['pass'])
        );

        $this->em->flush();

        return $user;
    }

    public function delete(User $user): void
    {
        $this->em->remove($user);
        $this->em->flush();
    }
}
