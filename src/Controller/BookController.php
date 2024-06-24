<?php
// src/Controller/BookController.php
namespace App\Controller;

use App\Entity\Book;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\BookRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
#[Route('/api/books')]
class BookController extends AbstractController
{
    private $entityManager;
    private $serializer;
    private $validator;

    public function __construct(EntityManagerInterface $entityManager, SerializerInterface $serializer, ValidatorInterface $validator)
    {
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
        $this->validator = $validator;
    }

    #[Route('/', methods: ['GET'])]
    public function list(): Response
    {
        $books = $this->entityManager->getRepository(Book::class)->findAll();
        $data = $this->serializer->serialize($books, 'json');
        return new Response($data, 200, ['Content-Type' => 'application/json']);
    }

    #[Route('/{id}', methods: ['GET'])]
    public function details($id): Response
    {
        $book = $this->entityManager->getRepository(Book::class)->find($id);
        if (!$book) {
            return new Response('Book not found', 404);
        }
        $data = $this->serializer->serialize($book, 'json');
        return new Response($data, 200, ['Content-Type' => 'application/json']);
    }

    #[Route('/addBook', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $inputConstraints = new Assert\Collection([
            'title' => [new Assert\NotBlank(), new Assert\Length(['min' => 1, 'max' => 255])],
            'author' => [new Assert\NotBlank(), new Assert\Length(['min' => 1, 'max' => 255])],
            'genre' => [new Assert\NotBlank(), new Assert\Length(['min' => 1, 'max' => 255])],
            'isbn' => [new Assert\NotBlank(), new Assert\Length(['min' => 1, 'max' => 255])],
            'publishedDate' => [new Assert\NotBlank(), new Assert\Date()],
            'status' => [new Assert\NotBlank(), new Assert\Choice(['choices' => ['Available', 'Borrowed']])]
        ]);

        $validationErrors = $this->validator->validate($data, $inputConstraints);
        if (count($validationErrors) > 0) {
            return new JsonResponse($this->serializer->serialize($validationErrors, 'json'), 400, ['Content-Type' => 'application/json']);
        }

        $book = new Book();
        $book->setTitle($data['title']);
        $book->setAuthor($data['author']);
        $book->setGenre($data['genre']);
        $book->setIsbn($data['isbn']);
        $book->setPublishedDate(new \DateTime($data['publishedDate']));
        $book->setStatus($data['status']);

        $errors = $this->validator->validate($book);
        if (count($errors) > 0) {
            return new JsonResponse($this->serializer->serialize($errors, 'json'), 400, ['Content-Type' => 'application/json']);
        }

        $this->entityManager->persist($book);
        $this->entityManager->flush();

        return new JsonResponse(['status' => 'Book added!', 'id' => $book->getId()], JsonResponse::HTTP_CREATED);
    }

    #[Route('/updateBook/{id}', methods: ['PUT'])]
    public function edit($id, Request $request): JsonResponse
    {
        $book = $this->entityManager->getRepository(Book::class)->find($id);
        if (!$book) {
            return new JsonResponse('Book not found', 404);
        }

        $data = json_decode($request->getContent(), true);

        $inputConstraints = new Assert\Collection([
            'title' => [new Assert\NotBlank(), new Assert\Length(['min' => 1, 'max' => 255])],
            'author' => [new Assert\NotBlank(), new Assert\Length(['min' => 1, 'max' => 255])],
            'genre' => [new Assert\NotBlank(), new Assert\Length(['min' => 1, 'max' => 255])],
            'isbn' => [new Assert\NotBlank(), new Assert\Length(['min' => 1, 'max' => 255])],
            'publishedDate' => [new Assert\NotBlank(), new Assert\Date()],
            'status' => [new Assert\NotBlank(), new Assert\Choice(['choices' => ['Available', 'Borrowed']])]
        ]);

        $validationErrors = $this->validator->validate($data, $inputConstraints);
        if (count($validationErrors) > 0) {
            return new JsonResponse($this->serializer->serialize($validationErrors, 'json'), 400, ['Content-Type' => 'application/json']);
        }

        $book->setTitle($data['title']);
        $book->setAuthor($data['author']);
        $book->setGenre($data['genre']);
        $book->setIsbn($data['isbn']);
        $book->setPublishedDate(new \DateTime($data['publishedDate']));
        $book->setStatus($data['status']);

        $errors = $this->validator->validate($book);
        if (count($errors) > 0) {
            return new JsonResponse($this->serializer->serialize($errors, 'json'), 400, ['Content-Type' => 'application/json']);
        }

        $this->entityManager->flush();

        return new JsonResponse(['status' => 'Book Updated!', 'id' => $book->getId()], JsonResponse::HTTP_CREATED);
    }

    #[Route('/deleteBook/{id}', methods: ['DELETE'])]
    public function delete($id): Response
    {
        $book = $this->entityManager->getRepository(Book::class)->find($id);
        if (!$book) {
            return new Response('Book not found', 404);
        }

        $this->entityManager->remove($book);
        $this->entityManager->flush();

        return new JsonResponse(['status' => 'Book Deleted!'], JsonResponse::HTTP_CREATED);
    }
}
