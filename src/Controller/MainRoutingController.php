<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\User;
use App\Repository\BookRepository;
use App\Repository\LibraryRepository;
use App\Service\OpenLibraryService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function Symfony\Component\String\b;

class MainRoutingController extends AbstractController
{
    private array $stylesheets;

    public function __construct()
    {
        $this->stylesheets[] = 'main.css';
    }
    /**
     * @Route("/", name="home")
     */
    #[route('', name: 'home0')]
    public function index(): Response
    {
        return $this->render('/home.html.twig');
    }

    #[route('/BookBinder/home', name: 'home')]
    public function loadHomePage(): Response
    {
        return $this->render('/home.html.twig');
    }

    public function home()
    {
        $this->loadHomePage();
    }

    #[route('/BookBinder/trending', name: 'books-trending')]
    public function loadTrendingBooksPage(BookRepository $bookRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $books = $bookRepository->findBy([], ['likes' => 'DESC']);
        $myUser = $this->getUser();
        $likedBooks = $myUser->getBooksLikedByUser();


        return $this->render('/trending-books.html.twig', ['books' => $books, 'likedBooks' => $likedBooks]);
    }


//    #[route('/BookBinder/book/{id}', name: 'book')]
    public function loadBookPage(BookRepository $bookRepository, $id): Response
    {
        $book = $bookRepository->findBy(['id' => $id], [], 1);
        return $this->render('book.html.twig', ['book' => $book]);
    }

    #[route('/BookBinder/about', name: 'about')]
    public function loadAboutPage(BookRepository $bookRepository): Response
    {
        return $this->render('/about.html.twig');
    }


}