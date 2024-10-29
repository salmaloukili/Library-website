<?php

namespace App\DataFixtures;

use BaseFixture;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;



class registrationFixtures extends Fixture
{

    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $connection = $manager->getConnection();
        $platform = $connection->getDatabasePlatform();
        $connection->executeUpdate($platform->getTruncateTableSQL('user', true));

        $user = new User();
        $user->setFirstname('Test');
        $user->setLastname('User');
        $user->setBirthdate(new \DateTime());
        $user->setRoles(['ROLE_USER']);
        $user->setEmail('test@test.com');
        $user->setUsername('testuser');
        $password = 'test1234';
        $user->setPassword($this->passwordHasher->hashPassword($user, $password));
        $user->setBio('Test bio');
        $user->setReadingListVisible(true);
        $user->setLikedBooksVisible(true);

        $manager->persist($user);
        $manager->flush();
    }
}