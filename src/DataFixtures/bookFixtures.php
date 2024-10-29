<?php

namespace App\DataFixtures;


use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use App\Entity\Book;


class bookFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $connection = $manager->getConnection();
        $platform = $connection->getDatabasePlatform();
        $connection->executeUpdate($platform->getTruncateTableSQL('book', true));

        for ($i = 1; $i <= 1001; $i++) {
            $book = new Book();
            $book->setId($i);
            $book->setISBN(1000000000 + $i);
            $book->setBookTitle('Book ' . $i);
            $book->setAuthorName('Author ' . $i);
            $book->setPublisher('Publisher ' . $i);
            $book->setGenre('Fake Genre ');
            $book->setNumberOfPages(rand(25, 1000));
            $book->setPublicationDate('1990-01-01');
            $book->setLanguage('Fake Language ');
            $book->setLikes(rand(0, 500));

            $manager->persist($book);
        }

        $manager->flush();
    }
}