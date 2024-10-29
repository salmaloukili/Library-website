<?php declare(strict_types=1);

namespace App\Tests\Controller;

use App\Entity\User;


use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Form\Test\TypeTestCase;



class MainRoutingControllerTest extends WebTestCase
{

    private function createAuthenticatedClient($username, $password)
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/BookBinder/login');

        $form = $crawler->selectButton('Login')->form();
        $form['email'] = $username;
        $form['password'] = $password;
        $client->submit($form);

        $client->followRedirect();

        return $client;
    }


    /**
     * @dataProvider provideUrls
     */
    public function testMainRoutingLogged($url)
    {
        $client = $this->createAuthenticatedClient('test@test.com', 'test1234');
        $client->request('GET', $url);

        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function provideUrls()
    {
        return [
            ['/BookBinder/home'],
            ['/BookBinder/login'],
            ['/register'],
            ['/BookBinder/feedback'],
            ['/BookBinder/about'],
            ['/BookBinder/explore'],
        ];
    }

    public function testHomePageBeforeLogin(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/BookBinder/home');

        $this->assertStringContainsString(
            'Login',
            $client->getResponse()->getContent()
        );

    }

    public function testHomePageAfterLogin(): void
    {
        $client = $this->createAuthenticatedClient('test@test.com', 'test1234');
        $crawler = $client->request('GET', '/BookBinder/home');

        $this->assertStringContainsString(
            'Logout',
            $client->getResponse()->getContent()
        );

    }

    public function testTrendingPageBeforeLogin(): void
    {
        $client = self::createClient();
        $crawler = $client->request('GET', '/BookBinder/trending');

        $this->assertTrue($client->getResponse()->isRedirect('/BookBinder/login'));

    }

    public function testTrendingPageAfterLogin(): void
    {
        $client = $this->createAuthenticatedClient('test@test.com', 'test1234');
        $crawler = $client->request('GET', '/BookBinder/trending');

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertStringContainsString(
            'Like what you like!',
            $client->getResponse()->getContent()
        );

        $crawler = $client->request('GET', '/BookBinder/book/1');
        $this->assertTrue($client->getResponse()->isSuccessful());

    }

    public function testSearchPageBeforeLogin(): void
    {
        $client = self::createClient();
        $crawler = $client->request('GET', '/BookBinder/search');

        $this->assertTrue($client->getResponse()->isRedirect('/BookBinder/login'));

    }

    public function testSearchPageAfterLogin(): void
    {
        $client = $this->createAuthenticatedClient('test@test.com', 'test1234');
        $crawler = $client->request('GET', '/BookBinder/search');

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertStringContainsString(
            'Search for your favorite books with different searchtypes!',
            $client->getResponse()->getContent()
        );

    }

}


