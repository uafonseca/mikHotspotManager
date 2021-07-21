<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }
    public function load(ObjectManager $manager)
    {
        $user = new User();
        

         $user->setPassword($this->passwordHasher->hashPassword(
             $user,
             '1234'
         ))
         ->setUsername('administrator')
         ->setIsLocal(true)
         ->addRole('ROLE_SUPER_ADMIN')
         ;

          
         $manager->persist($user);
        $manager->flush();
    }
}
