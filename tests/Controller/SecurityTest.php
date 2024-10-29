<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Form\FormFactoryInterface;


class SecurityTest extends WebTestCase
{
    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */

    public function testRegistrationAndLogin()
    {

        $client = static::createClient();
        $crawler = $client->request('GET', '/register');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Register')->form([
            'registration_form[firstname]' => 'Test',
            'registration_form[lastname]' => 'User',
            'registration_form[birthdate][year]' => '2001',
            'registration_form[birthdate][month]' => '10',
            'registration_form[birthdate][day]' => '3',
            'registration_form[username]' => 'testuserByControllerTest',
            'registration_form[email]' => 'test@test.test',
            'registration_form[agreeTerms]' => true,
            'registration_form[plainPassword]' => [
                'first' => 'test1234',
                'second' => 'test1234',
            ],
        ]);

        $client->submit($form);

// Test login with the newly registered user
        $crawler = $client->request('GET', '/BookBinder/login');

        $form = $crawler->selectButton('Login')->form();
        $form['email'] = 'test@test.test';
        $form['password'] = 'test1234';

        $client->submit($form);

        $this->assertTrue($client->getResponse()->isRedirect('/BookBinder/home'));

        $client->followRedirect();

        $crawler = $client->request('GET', '/BookBinder/profile/2');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertStringContainsString(
            'test@test.test',
            $client->getResponse()->getContent()
        );

// Try to register with the same email
        $crawler = $client->request('GET', '/register');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Register')->form([
            'registration_form[firstname]' => 'Test',
            'registration_form[lastname]' => 'User',
            'registration_form[birthdate][year]' => '2001',
            'registration_form[birthdate][month]' => '10',
            'registration_form[birthdate][day]' => '3',
            'registration_form[username]' => 'testuserByControllerTest',
            'registration_form[email]' => 'test@test.test',
            'registration_form[agreeTerms]' => true,
            'registration_form[plainPassword]' => [
                'first' => 'test1234',
                'second' => 'test1234',
            ],
        ]);

        $client->submit($form);

        $this->assertStringContainsString(
            'There is already an account with this email',
            $client->getResponse()->getContent()
        );


    }


    public function testResgisterWithUnmatchingPW(){

        // Try to register with unmatching passwords
        $client = static::createClient();

        $crawler = $client->request('GET', '/register');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Register')->form([
            'registration_form[firstname]' => 'Test',
            'registration_form[lastname]' => 'User',
            'registration_form[birthdate][year]' => '2001',
            'registration_form[birthdate][month]' => '10',
            'registration_form[birthdate][day]' => '3',
            'registration_form[username]' => 'testuserByControllerTest',
            'registration_form[email]' => 'test@test.test',
            'registration_form[agreeTerms]' => true,
            'registration_form[plainPassword]' => [
                'first' => 'test12345',
                'second' => 'test1234',
            ],
        ]);

        $client->submit($form);

        $this->assertStringContainsString(
            'The values do not match.',
            $client->getResponse()->getContent()
        );
    }


    public function testResgisterWithShortPW(){

        // Try to register with unmatching passwords
        $client = static::createClient();

        $crawler = $client->request('GET', '/register');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Register')->form([
            'registration_form[firstname]' => 'Test',
            'registration_form[lastname]' => 'User',
            'registration_form[birthdate][year]' => '2001',
            'registration_form[birthdate][month]' => '10',
            'registration_form[birthdate][day]' => '3',
            'registration_form[username]' => 'testuserByControllerTest',
            'registration_form[email]' => 'test@test.test',
            'registration_form[agreeTerms]' => true,
            'registration_form[plainPassword]' => [
                'first' => 'test12345',
                'second' => 'test1234',
            ],
        ]);

        $client->submit($form);

        $this->assertStringContainsString(
            'The values do not match.',
            $client->getResponse()->getContent()
        );
    }


    public function testLoginWithInvalidCredentials()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/BookBinder/login');

        $form = $crawler->selectButton('Login')->form();
        $form['email'] = 'invalid@kul.com';
        $form['password'] = 'invalid';

        $client->submit($form);

        $crawler = $client->followRedirect();

        // Check if the error message is displayed
        $errorMessageElement = $crawler->filter('.alert.alert-danger');
        $this->assertEquals(1, $errorMessageElement->count());
    }

}