<?php
// src/Controller/BorrowController.php
namespace App\Controller;

use App\Entity\Borrow;
use App\Entity\Book;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('/api/borrows')]
class BorrowController extends AbstractController
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
        $borrows = $this->entityManager->getRepository(Borrow::class)->findAll();
        $data = $this->serializer->serialize($borrows, 'json');
        return new Response($data, 200, ['Content-Type' => 'application/json']);
    }

    #[Route('/{id}', methods: ['GET'])]
    public function details(int $id): Response
    {
        $borrow = $this->entityManager->getRepository(Borrow::class)->find($id);
        if (!$borrow) {
            return new Response('Borrow record not found', 404);
        }
        $data = $this->serializer->serialize($borrow, 'json');
        return new Response($data, 200, ['Content-Type' => 'application/json']);
    }

    #[Route('/history/{userId}', name: 'borrow_history', methods: ['GET'])]
    public function viewBorrowHistory(int $userId): JsonResponse
    {
        // Fetch borrow history for the given user ID
        // This is a placeholder; implement actual logic to retrieve borrow history
        $borrowHistory = [
            [
                'id' => 1,
                'bookId' => 1,
                'userId' => $userId,
                'borrowDate' => '2024-07-25',
                'returnDate' => '2024-08-25',
                'status' => 'Returned'
            ],
            // Add more borrow history records as needed
        ];

        $jsonContent = $this->serializer->serialize($borrowHistory, 'json');

        return new JsonResponse($jsonContent, JsonResponse::HTTP_OK, [], true);
    }

    #[Route('/borrowBook', methods: ['POST'])]
    public function borrow(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $inputConstraints = new Assert\Collection([
            'book_id' => [new Assert\NotBlank(), new Assert\Type('integer')],
            'user_id' => [new Assert\NotBlank(), new Assert\Type('integer')],
            'borrow_date' => [new Assert\NotBlank(), new Assert\Type('string')],
        ]);

        $validationErrors = $this->validator->validate($data, $inputConstraints);
        if (count($validationErrors) > 0) {
            return new JsonResponse($this->serializer->serialize($validationErrors, 'json'), 400, ['Content-Type' => 'application/json']);
        }

        $book = $this->entityManager->getRepository(Book::class)->find($data['book_id']);
        $user = $this->entityManager->getRepository(User::class)->find($data['user_id']);

        if (!$book) {
            return new JsonResponse(['error' => 'Book not found'], 404);
        }

        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], 404);
        }

        if ($book->getStatus() === 'Borrowed') {
            return new JsonResponse(['error' => 'Book is already borrowed'], 400);
        }

        try {
            $borrowDate = \DateTime::createFromFormat('Y-m-d H:i:s', $data['borrow_date']);
            if (!$borrowDate) {
                throw new \Exception('Invalid date format');
            }
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Invalid borrow_date format. Use Y-m-d H:i:s.'], 400, ['Content-Type' => 'application/json']);
        }

        $borrow = new Borrow();
        $borrow->setBook($book);
        $borrow->setUser($user);
        $borrow->setBorrowDate($borrowDate);
        $borrow->setReturnDate(null);

        $errors = $this->validator->validate($borrow);
        if (count($errors) > 0) {
            return new JsonResponse($this->serializer->serialize($errors, 'json'), 400, ['Content-Type' => 'application/json']);
        }

        $book->setStatus('Borrowed');

        $this->entityManager->persist($borrow);
        $this->entityManager->flush();

        return new JsonResponse(['status' => 'Book borrowed!', 'id' => $borrow->getId()], JsonResponse::HTTP_CREATED);
    }

    #[Route('/returnBook/{id}', methods: ['PUT'])]
    public function return(int $id, Request $request): JsonResponse
    {
        $borrow = $this->entityManager->getRepository(Borrow::class)->find($id);
        if (!$borrow) {
            return new JsonResponse(['error' => 'Borrow record not found'], 404);
        }

        $data = json_decode($request->getContent(), true);

        $inputConstraints = new Assert\Collection([
            'return_date' => [new Assert\NotBlank(), new Assert\Type('string')],
        ]);

        $validationErrors = $this->validator->validate($data, $inputConstraints);
        if (count($validationErrors) > 0) {
            return new JsonResponse($this->serializer->serialize($validationErrors, 'json'), 400, ['Content-Type' => 'application/json']);
        }

        try {
            $currentDateTime = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
            $returnDate=new Response($currentDateTime->format('Y-m-d H:i:s'));
            if (!$returnDate) {
                throw new \Exception('Invalid date format');
            }
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Invalid return_date format. Use Y-m-d H:i:s.'], 400, ['Content-Type' => 'application/json']);
        }

        $borrow->setReturnDate($currentDateTime);
        $borrow->getBook()->setStatus('Available');

        // $errors = $this->validator->validate($borrow);
        // if (count($errors) > 0) {
        //     return new JsonResponse($this->serializer->serialize($errors, 'json'), 400, ['Content-Type' => 'application/json']);
        // }

        $this->entityManager->flush();

        return new JsonResponse(['status' => 'Book returned!', 'id' => $borrow->getId()], JsonResponse::HTTP_OK);
    }

    #[Route('/deleteBorrow/{id}', methods: ['DELETE'])]
    public function delete(int $id): Response
    {
        $borrow = $this->entityManager->getRepository(Borrow::class)->find($id);
        if (!$borrow) {
            return new Response('Borrow record not found', 404);
        }

        $this->entityManager->remove($borrow);
        $this->entityManager->flush();

        return new JsonResponse(['status' => 'Borrow record deleted!'], JsonResponse::HTTP_OK);
    }
}
