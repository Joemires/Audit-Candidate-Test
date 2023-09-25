<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

class AuthenticatableController extends AbstractFOSRestController
{
    #[Route('/api/auth/register', methods: ['POST'])]
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();

        $form = $this->container->get('form.factory')->createNamed('', UserType::class, $user, [
            'csrf_protection' => false,
        ]);

        // dd($request->getLocale());
        // dd(date_default_timezone_get());

        $form->handleRequest($request);

        if(! $form->isSubmitted() || ! $form->isValid()) {
            return $this->handleView($this->view($form, Response::HTTP_UNPROCESSABLE_ENTITY));
        }

        $user->setPassword(
            $passwordHasher->hashPassword(
                $user,
                $form->get('password')->getData()
            )
        );

        $user->setCreatedAt();
        $user->setUpdatedAt();

        // tell Doctrine you want to (eventually) save the Product (no queries yet)
        $entityManager->persist($user);
        
        // actually executes the queries (i.e. the INSERT query)
        $entityManager->flush();

        return $this->handleView($this->view($user, Response::HTTP_CREATED));
    }
    
    #[Route('/api/auth/login', methods: ['POST'])]
    public function login(#[CurrentUser] ?User $user, JWTTokenManagerInterface $JWTManager): Response
    {
        if (null === $user) {
            return $this->json([
                'message' => 'missing credentials',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $token = $JWTManager->create($user); // somehow create an API token for $user
            
        return $this->json([
            'user' => $user->getUserIdentifier(),
            'token' => $token,
        ]);
    }

    #[Route('/api/auth/user', methods: ['GET'])]
    public function user(#[CurrentUser] ?User $user, JWTTokenManagerInterface $JWTManager): Response
    {
        return $this->handleView($this->view($this->getUser(), Response::HTTP_OK));
    }
}
