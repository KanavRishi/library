<?php
// src/Controller/UserController.php
namespace App\Controller;

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

#[Route('/api/users')]
class UserController extends AbstractController
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
        $users = $this->entityManager->getRepository(User::class)->findAll();
        $data = $this->serializer->serialize($users, 'json');
        return new Response($data, 200, ['Content-Type' => 'application/json']);
    }

    #[Route('/{id}', methods: ['GET'])]
    public function details($id): Response
    {
        $user = $this->entityManager->getRepository(User::class)->find($id);
        if (!$user) {
            return new Response('User not found', 404);
        }
        $data = $this->serializer->serialize($user, 'json');
        return new Response($data, 200, ['Content-Type' => 'application/json']);
    }

    #[Route('/addUser', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $inputConstraints = new Assert\Collection([
            'name' => [new Assert\NotBlank(), new Assert\Length(['min' => 1, 'max' => 255])],
            'email' => [new Assert\NotBlank(), new Assert\Email()],
            'role' => [new Assert\NotBlank(), new Assert\Choice(['choices' => ['Admin', 'Member']])],
            'password' => [new Assert\NotBlank(), new Assert\Length(['min' => 6])]
        ]);

        $validationErrors = $this->validator->validate($data, $inputConstraints);
        if (count($validationErrors) > 0) {
            return new JsonResponse([$this->serializer->serialize($validationErrors, 'json')], 400, ['Content-Type' => 'application/json']);
        }

        $user = new User();
        $user->setName($data['name']);
        $user->setEmail($data['email']);
        $user->setRole($data['role']);
        $user->setPassword(password_hash($data['password'], PASSWORD_BCRYPT));

        $errors = $this->validator->validate($user);
        if (count($errors) > 0) {
            return new JsonResponse($this->serializer->serialize($errors, 'json'), 400, ['Content-Type' => 'application/json']);
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return new JsonResponse(['status' => 'User added!', 'id' => $user->getId()], JsonResponse::HTTP_CREATED);
    }

    #[Route('/updateUser/{id}', methods: ['PUT'])]
    public function edit($id, Request $request): JsonResponse
    {
        $user = $this->entityManager->getRepository(User::class)->find($id);
        if (!$user) {
            return new JsonResponse('User not found', 404);
        }

        $data = json_decode($request->getContent(), true);

        $inputConstraints = new Assert\Collection([
            'name' => [new Assert\NotBlank(), new Assert\Length(['min' => 1, 'max' => 255])],
            'email' => [new Assert\NotBlank(), new Assert\Email()],
            'role' => [new Assert\NotBlank(), new Assert\Choice(['choices' => ['Admin', 'Member']])],
            'password' => [new Assert\Optional([new Assert\Length(['min' => 6])])]
        ]);

        $validationErrors = $this->validator->validate($data, $inputConstraints);
        if (count($validationErrors) > 0) {
            return new JsonResponse($this->serializer->serialize($validationErrors, 'json'), 400, ['Content-Type' => 'application/json']);
        }

        $user->setName($data['name']);
        $user->setEmail($data['email']);
        $user->setRole($data['role']);
        if (isset($data['password'])) {
            $user->setPassword(password_hash($data['password'], PASSWORD_BCRYPT));
        }

        $errors = $this->validator->validate($user);
        if (count($errors) > 0) {
            return new JsonResponse($this->serializer->serialize($errors, 'json'), 400, ['Content-Type' => 'application/json']);
        }

        $this->entityManager->flush();

        return new JsonResponse(['status' => 'User updated!', 'id' => $user->getId()], JsonResponse::HTTP_OK);
    }

    #[Route('/deleteUser/{id}', methods: ['DELETE'])]
    public function delete($id): Response
    {
        $user = $this->entityManager->getRepository(User::class)->find($id);
        if (!$user) {
            return new Response('User not found', 404);
        }

        $this->entityManager->remove($user);
        $this->entityManager->flush();

        return new JsonResponse(['status' => 'User deleted!'], JsonResponse::HTTP_OK);
    }
}
