<?php

namespace App\Tests\Controller;

use App\Controller\MainFormController;
use App\Entity\Book;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;



class BookControllerTest extends WebTestCase
{
    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */

    public function testCommentFunctionality()
    {
        $client = static::createClient();

        // Simulate a login
        $client->loginUser($client->getContainer()->get('App\Repository\UserRepository')->findOneBy([]));

        $crawler = $client->request('GET', '/BookBinder/trending');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $crawler = $client->request('GET', '/BookBinder/book/1');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Post')->form([
            'comment_form[text]' => 'This is a test comment',
        ]);

        $client->submit($form);

        $crawler = $client->request('GET', '/BookBinder/book/1');

        $this->assertStringContainsString(
            'This is a test comment',
            $client->getResponse()->getContent()
        );

    }

    public function testLikeExistingBook(): void
    {
        $client = static::createClient();
        $client->loginUser($client->getContainer()->get('App\Repository\UserRepository')->findOneBy([]));

        $bookRepository = $client->getContainer()->get('App\Repository\BookRepository');
        $bookBeforeLike = $bookRepository->find(1);
        $likesBefore = $bookBeforeLike->getLikes();

        $crawler = $client->request('GET', '/BookBinder/trending');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $client->request('POST', '/book/1' . '/like');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $bookAfterLike = $bookRepository->find(1);
        $likesAfter = $bookAfterLike->getLikes();

        $this->assertEquals($likesBefore + 1, $likesAfter, 'The likes count did not increment.');

        $crawler = $client->request('GET', '/BookBinder/profile/1');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString(
            'Book 1',
            $client->getResponse()->getContent()
        );
    }

    public function testLikeNewFakeBook(): void
    {
        $client = static::createClient();
        $client->loginUser($client->getContainer()->get('App\Repository\UserRepository')->findOneBy([]));

        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');

        $book = new Book();
        $book->setBookTitle('Fake Book');
        $book->setAuthorName('Fake Author');
        $book->setPublisher('Fake Publisher');
        $book->setNumberOfPages(100);
        $book->setLikes(0);
        $book->setISBN(0000000000);
        $book->setGenre("Fake genre");
        $book->setPublicationDate('2000-01-01');
        $book->setLanguage('FL');

        $entityManager->persist($book);
        $entityManager->flush();

        $bookId = $book->getId();

        $crawler = $client->request('GET', '/BookBinder/trending');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $client->request('POST', '/book/' . $bookId . '/like');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $bookAfterLike = $entityManager->getRepository(Book::class)->find($bookId);
        $likesAfter = $bookAfterLike->getLikes();

        $this->assertEquals(1, $likesAfter, 'The likes count did not increment.');

        $crawler = $client->request('GET', '/BookBinder/profile/1');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString(
            'Fake Book',
            $client->getResponse()->getContent()
        );

        $crawler = $client->request('GET', '/BookBinder/book/' . $bookId);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }


    public function testSearchFunctionality(): void
    {
        /**
         * @runInSeparateProcess
         * @preserveGlobalState disabled
         */

        $client = static::createClient();
        $client->loginUser($client->getContainer()->get('App\Repository\UserRepository')->findOneBy([]));

        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');

        $book = new Book();
        $book->setBookTitle('Test Book');
        $book->setAuthorName('Test Author');
        $book->setPublisher('Fake Publisher');
        $book->setNumberOfPages(100);
        $book->setLikes(0);
        $book->setISBN(0000000000);
        $book->setGenre("Fake genre");
        $book->setPublicationDate('2000-01-01');
        $book->setLanguage('FL');

        $entityManager->persist($book);
        $entityManager->flush();


        $crawler = $client->request('GET', '/BookBinder/search');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Search')->form();

        $form['search_book_form[type]'] ='bookTitle';
        $form['search_book_form[value]'] = 'Test Book';

        $crawler = $client->submit($form);

        $this->assertStringContainsString(
            'Test Book',
            $client->getResponse()->getContent()
        );


    }

}
