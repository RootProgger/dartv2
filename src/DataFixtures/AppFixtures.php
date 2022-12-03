<?php

namespace App\DataFixtures;

use App\Entity\Tenancy;
use App\Entity\User;
use App\Enum\UserRole;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher){}

    public function load(ObjectManager $manager): void
    {
        $tenancy = (new Tenancy)
            ->setSiteUrl('localhost')
            ->setSiteName('Administration')
            ->setDefault(true)
            ;
        $manager->persist($tenancy);

        $user = (new User)
            ->setTenancy($tenancy)
            ->setEmail('m@o.de')
            ->setFirstname('Marcus')
            ->setLastname('Wolf')
            ->addRole(UserRole::SUPER_ADMIN)
                ;
        $password = $this->passwordHasher->hashPassword($user, '123');
        $user->setPassword($password);

        $manager->persist($user);

        $manager->flush();
    }
}
