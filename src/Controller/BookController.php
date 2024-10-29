<?php

namespace App\Controller;

use App\Entity\Book;
use App\Repository\BookRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/book/{id}/like", name="book_like", methods={"POST"})
     */
    public function like(UserRepository $userRepository, $id): JsonResponse
    {
        // Retrieve the book from the database based on the given $id
        // Increment the 'likes' count for the book by one
        // Save the updated book to the database
        $book = $this->entityManager->getRepository(Book::class)->find($id);

        if (!$book) {
            throw $this->createNotFoundException('Book not found.');
        }

        $likes = $book->getLikes();
        $book->setLikes($likes + 1);

        // Retrieve the user from the database based on login
        // Link the liked book to the user that is logged in
        $user = $userRepository->findByIdentifier($this->getUser()->getUserIdentifier());
        $user->addBooksLikedByUser($book);

        $this->entityManager->flush();

        return new JsonResponse(['success' => true]);
    }
}
