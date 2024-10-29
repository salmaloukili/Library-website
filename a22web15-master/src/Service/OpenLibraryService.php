<?php

namespace App\Service;

use App\Entity\Book;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class OpenLibraryService
{
    private HttpClientInterface $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function getBook(string $isbn): Book
    {
//        implode(",", $parsedResponse['subjects'])
        $url = 'https://openlibrary.org/isbn/' . $isbn . '.json';

        $response = $this->client->request('GET', $url);
        $parsedResponse = $response->toArray();
        $book = new Book();
        $book->setBookTitle($parsedResponse['title']);
        $book->setGenre($parsedResponse['genres'][0]);
        $book->setISBN($parsedResponse['isbn_13'][0]);
        $book->setPublisher(implode(",", $parsedResponse['publishers']));
        $book->setPublicationDate($parsedResponse['publish_date']);
        $book->setNumberOfPages($parsedResponse['number_of_pages']);
        $book->setAuthorName($this->getAuthor($parsedResponse['authors'][0]['key']));
        $book->setLanguage($this->getLanguage($parsedResponse['languages'][0]['key']));
        return $book;
    }

    public function getAuthor(string $id): string
    {
        $url = 'https://openlibrary.org' . $id . '.json';
        $response = $this->client->request('GET', $url);
        $parsedResponse = $response->toArray();
        return $parsedResponse['name'];
    }

    public function getLanguage($id): string
    {
        $url = 'https://openlibrary.org' . $id . '.json';
        $response = $this->client->request('GET', $url);
        $parsedResponse = $response->toArray();
        return $parsedResponse['name'];
    }
}