<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Service\UserService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route(path: '/v1/api/users', name: 'api_')]
class UserController extends AbstractController
{
    public function __construct(
        private UserService $userService,
        private SerializerInterface $serializer,
        private ValidatorInterface $validator,
    ) {
    }

    private function validateEntity(User $user): ?JsonResponse
    {
        $errors = $this->validator->validate($user);

        if (count($errors) > 0) {
            $errorMessages = [];

            foreach ($errors as $error) {
                $errorMessages[] = [
                    'field' => $error->getPropertyPath(),
                    'message' => $error->getMessage(),
                ];
            }

            return $this->json([
                'status' => 'Bad Request',
                'code' => JsonResponse::HTTP_BAD_REQUEST,
                'errors' => $errorMessages,
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        return null;
    }

    private function createUserFromRequest(array $data, ?User $user = null): User
    {
        $user = $user ?? new User();

        $user->setLogin($data['login'] ?? '');
        $user->setPhone($data['phone'] ?? '');
        $user->setPassword($data['pass'] ?? '');

        return $user;
    }

    /**
     * Get list of users
     */
    #[Route('', name: 'users_index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $users = $this->userService->getAll();
        $data = $this->serializer->normalize($users, null, ['groups' => ['user:read']]);

        return $this->json(['data' => $data], JsonResponse::HTTP_OK);
    }

    /**
     * Get user by ID
     */
    #[Route('/{id}', name: 'users_show', methods: ['GET'])]
    #[IsGranted('USER_VIEW', subject: 'user', message: 'You are not allowed to view other users.')]
    public function show(User $user): JsonResponse
    {
        $data = [
            'id' => $user->getId(),
            'login' => $user->getLogin(),
            'phone' => $user->getPhone(),
        ];

        return $this->json($data, JsonResponse::HTTP_OK);
    }

    /**
     * Create new user
     */
    #[Route('', name: 'users_store', methods: ['POST'])]
    #[IsGranted('USER_CREATE', message: 'You are not allowed to create users.')]
    public function store(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true) ?? [];
        $user = $this->createUserFromRequest($data);

        if ($response = $this->validateEntity($user)) {
            return $response;
        }

        $user = $this->userService->create($data);

        return $this->json([
            'id' => $user->getId(),
            'login' => $user->getLogin(),
            'phone' => $user->getPhone(),
        ], JsonResponse::HTTP_CREATED);
    }

    /**
     * Update user
     */
    #[Route('/{id}', name: 'users_update', methods: ['PUT'])]
    #[IsGranted('USER_EDIT', subject: 'user', message: 'You are not allowed to edit other users.')]
    public function update(User $user, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true) ?? [];
        $user = $this->createUserFromRequest($data, $user);

        if ($response = $this->validateEntity($user)) {
            return $response;
        }

        $user = $this->userService->update($user, $data);

        return $this->json(['id' => $user->getId()], JsonResponse::HTTP_OK);
    }

    /**
     * Delete user
     */
    #[Route('/{id}', name: 'users_destroy', methods: ['DELETE'])]
    #[IsGranted('USER_DELETE', subject: 'user', message: 'You are not allowed to delete users.')]
    public function destroy(User $user): JsonResponse
    {
        $this->userService->delete($user);

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }
}
