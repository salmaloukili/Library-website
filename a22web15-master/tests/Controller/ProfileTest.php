<?php

namespace App\Tests\Controller;


use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\User;
use DateTime;
use DateTimeInterface;



class ProfileTest extends WebTestCase
{
    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */

    public function testProfilePage()
    {
        $client = static::createClient();

        // Simulate a login, this function returns always the first user in the database, hence why we request /profile/1 because we know that the first user always has id=1
        $client->loginUser($client->getContainer()->get('App\Repository\UserRepository')->findOneBy([]));

        $crawler = $client->request('GET', '/BookBinder/profile/1');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertStringContainsString(
            'testuser',
            $client->getResponse()->getContent()
        );
        $this->assertStringContainsString(
            'test@test.com',
            $client->getResponse()->getContent()
        );

    }


    public function testProfileEdit(): void
    {
        $client = static::createClient();
        $client->loginUser($client->getContainer()->get('App\Repository\UserRepository')->findOneBy([]));

        $crawler = $client->request('GET', '/BookBinder/edit-profile');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Save')->form();
        $form['form[username]'] = 'Test Name';
        $form['form[bio]'] = 'This is a test bio';
        $form['form[likedBooksVisible]'] = true;

        $client->submit($form);

        // Assert that the form redirects to the profile page
        $this->assertTrue($client->getResponse()->isRedirect('/BookBinder/profile/1'));

        $crawler = $client->followRedirect();

        $this->assertStringContainsString(
            'Test Name',
            $client->getResponse()->getContent()
        );

        $this->assertStringContainsString(
            'This is a test bio',
            $client->getResponse()->getContent()
        );

        $this->assertStringContainsString(
            'public',
            $client->getResponse()->getContent()
        );
    }

//    public function testProfileEditWithEmptyDB(): void
//    {
//        $client = static::createClient();
//
//        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');
//
//        $user = new User();
//        $user->setFirstname('John');
//        $user->setLastname('Doe');
//        $user->setBirthdate(new DateTime('1984-04-15 00:00:00'));
//        $user->setEmail('john.doe@example.com');
//        $user->setRoles(['ROLE_USER']);
//        $user->setPassword('password123');
//        $user->setUsername('johndoe');
//        $user->setBio('hi');
//        $user->setLikedBooksVisible(true);
//
////        $userRepository = new UserRepository($user);
////        $userRepository->save($user, true);
//        $entityManager->persist($user);
//        $entityManager->flush();
//
//        $userId = $user->getId();
//
//        $crawler = $client->request('GET', '/BookBinder/login');
//
//        $form = $crawler->selectButton('Login')->form();
//        $form['email'] = 'john.doe@example.com';
//        $form['password'] = 'password123';
//
//        $client->submit($form);
//
//        $client->followRedirect();
//
//        $crawler = $client->request('GET', '/BookBinder/profile/'.$userId);
//        $this->assertEquals(200, $client->getResponse()->getStatusCode());
//    }

}
