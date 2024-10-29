<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Comment;
use App\Entity\User;
use App\Entity\Feedback;
use App\Form\CommentFormType;
use App\Form\SearchBookFormType;
use App\Repository\BookRepository;
use App\Repository\CommentRepository;
use App\Repository\UserRepository;
use App\Repository\FeedbackRepository;
use App\Service\OpenLibraryService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Length;
use function Symfony\Component\String\b;

class MainFormController extends AbstractController
{

    #[route('/BookBinder/search', name: 'books-search')]
    public function loadSearchBooksPage(OpenLibraryService $openLibrary, BookRepository $bookRepository, Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $searchBookForm = $this->createForm(SearchBookFormType::class);

        $searchBookForm->handleRequest($request);
//        if ($searchBookForm->isSubmitted()) {
//            $searchType = $searchBookForm->get('type')->getData();
//            $searchValue = $searchBookForm->get('value')->getData();
//
//            $books = $bookRepository->findBy([$searchType => $searchValue], []);
//            return $this->render('/search-books.html.twig', ['search_book_form' => $searchBookForm->createView(), 'books' => $books]);
//        }
//        return $this->render('/search-books.html.twig', ['search_book_form' => $searchBookForm->createView(), 'books' => " "]);
        $books = [];
        if ($searchBookForm->isSubmitted()) {
            $searchType = $searchBookForm->get('type')->getData();
            $searchValue = $searchBookForm->get('value')->getData();


            if ($searchType === 'bookTitle' || $searchType === 'authorName') {
                $books = $bookRepository->findByTitleOrAuthor($searchValue);
            } else if ($searchType === 'publisher' || $searchType === 'genre' || $searchType === 'language' || $searchType === 'publicationDate'){
                $books = $bookRepository->findByPartialMatch($searchType, $searchValue);
            } else {
                $books = $bookRepository->findBy([$searchType => $searchValue], []);
            }
        }
            return $this->render('/search-books.html.twig', ['search_book_form' => $searchBookForm->createView(), 'books' => $books]);

    }

    #[route('/BookBinder/book/{id}', name: 'book')]
    public function loadBookPage(Request $request, BookRepository $bookRepository, $id, UserRepository $userRepository, CommentRepository $commentRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $book = $bookRepository->findBy(['id' => $id], [], 1);
        $comments = $commentRepository->findBy(['bookId' => $id]);
        $myUser = $this->getUser();
        $likedBooks = $myUser->getBooksLikedByUser();

        $comment = new Comment();
        $commentForm = $this->createForm(CommentFormType::class, $comment);
        $commentForm->handleRequest($request);

        if ($commentForm->isSubmitted() && $commentForm->isValid()) {

            $comment->setBookId($book[0]);

            $user = $userRepository->findByIdentifier($this->getUser()->getUserIdentifier());
            $comment->setUserId($user);
            $comment->setText($commentForm->get('text')->getData());

            $commentRepository->save($comment, true);
            return $this->redirectToRoute('book', ['id' => $id]);

        }
        return $this->render('/book.html.twig', ['bookForm' => $commentForm->createView(), 'book' => $book, 'comments' => $comments, 'likedBooks' => $likedBooks]);

    }


    #[Route('/BookBinder/profile/{id}', name: 'profile')]
    public function viewUserProfile($id, UserRepository $userRepository): Response
    {
        $userArray = $userRepository->findBy(['id' => $id], [], 1);
        $user = $userArray[0];
        if ($user) {
            $books = $user->getBooksLikedByUser();
            return $this->render('profile.html.twig', [
                'user' => $user, 'books' => $books
            ]);
        } else {
            return $this->redirectToRoute('app_login'); // Redirect to login if user is not authenticated
        }
    }

    #[Route("/BookBinder/edit-profile", name: "edit-profile")]
    public function profile(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser(); // Get the currently logged-in user

        // Check if the user is logged in
        if ($user == null) {
            return $this->redirectToRoute('app_login');
        }

        // create form
        $form = $this->createFormBuilder()
            ->add('username', TextType::class, [
            'attr' => ['class' => 'name-edit'],
            ])

            ->add('bio', TextareaType::class, [
            'attr' => ['class' => 'bio-edit'],'label' => 'Bio:',

                'constraints' => [
                    new Length([
                        'max' => 255, // Set the maximum character limit
                        'maxMessage' => 'Your bio must not exceed 255 characters.',
                    ]),
                ],
            ])

            ->add('likedBooksVisible', ChoiceType::class, [
                'choices' => [
                    'Public' => true,
                    'Private' => false,
                ],
                'expanded' => true,
                'multiple' => false,

                'attr' => ['class' => 'likedBooks-edit'],
            ])
            ->add('save', SubmitType::class, ['label' => 'Save',
                'attr' => ['class' => 'save'],
                ])
            ->getForm();

        // Set the initial form data with the user's current values
        $form->setData([
            'username' => $user->getUsername(),
            'bio' => $user->getBio(),
            'likedBooksVisible' => $user->isLikedBooksVisible(),
        ]);

        // check if form was submitted and handle data
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            // Update the user object with the new values
            $user->setUsername($data['username']);
            $user->setBio($data['bio']);
            $user->setLikedBooksVisible($data['likedBooksVisible']);

            $em->flush();

            return $this->redirectToRoute('profile', ['id' => $user->getId()]);
        }

        return $this->render('edit-profile.html.twig', [
            'profile_form' => $form->createView(),
        ]);


    }

    #[route('/BookBinder/feedback', name: 'feedback')]
    public function loadFeedbackPage(Request $request, EntityManagerInterface $em): Response
    {

        $feedback = new Feedback();
        $form = $this->createFormBuilder($feedback)
            ->add('name', TextType::class)
            ->add('email', TextType::class)
            ->add('phone_number', TextType::class)
            ->add('message',TextareaType::class, ['label' => 'Massage'])
            ->add('save',SubmitType::class, ['label' => 'Submit Message'])
            ->getForm();

// check if form was submitted and handle data
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $feedback = $form->getData();
            $em->persist($feedback);
            $em->flush();
            // Add a flash message
            $this->addFlash(
                'success', // Flash message type
                'Your feedback has been successfully sent!' // Flash message content
            );
            return $this->redirect('/BookBinder/feedback');
        }
        return $this->render('feedback.html.twig',[

            'feedback_form'=>$form
        ]);

    }

    #[Route('/BookBinder/explore', name: 'books-explore')]
    public function exploreBooks(BookRepository $bookRepository): Response
    {
        $books = $bookRepository->findAll();

        return $this->render('explore.html.twig', [
            'books' => $books,
        ]);
    }

}
