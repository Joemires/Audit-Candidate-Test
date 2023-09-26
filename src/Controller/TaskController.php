<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TaskController extends AbstractFOSRestController
{   
    #[Route('/api/tasks', methods: ['GET'])]
    public function index(): Response
    {
        $tasks = $this->getUser()->getTasks();

        return $this->json(
            ['status' => 200, 'message' => 'Tasks retrieved', 'data' => $tasks],
            Response::HTTP_OK,
            [],
            [ObjectNormalizer::IGNORED_ATTRIBUTES => ['user']]
        );
    }
    
    #[Route('/api/tasks', methods: ['POST'])]
    public function store(Request $request, EntityManagerInterface $entityManager): Response
    {
        date_default_timezone_set($this->getUser()->getTimezone());

        $task = new Task();

        $form = $this->container->get('form.factory')->createNamed('', TaskType::class, $task, [
            'csrf_protection' => false,
        ]);

        $form->handleRequest($request);

        if(! $form->isSubmitted() || ! $form->isValid()) {
            return $this->handleView($this->view($form, Response::HTTP_UNPROCESSABLE_ENTITY));
        }
        
        $task->setUser($this->getUser());
        
        $task->setCreatedAt();
        $task->setUpdatedAt();
        
        // tell Doctrine you want to (eventually) save the Product (no queries yet)
        $entityManager->persist($task);
        
        // actually executes the queries (i.e. the INSERT query)
        $entityManager->flush();
        
        $view = $this->view($task, Response::HTTP_UNPROCESSABLE_ENTITY);
        $view->setContext([ObjectNormalizer::IGNORED_ATTRIBUTES => ['user']]);
        
        return $this->json(
            ['status' => 200, 'message' => 'Task saved successfully', 'data' => $task],
            Response::HTTP_CREATED,
            [],
            
        );
    }

    #[Route('/api/tasks/{task}', methods: ['PATCH'])]
    public function markCompleted(EntityManagerInterface $entityManager, Task $task): Response
    {
        date_default_timezone_set($this->getUser()->getTimezone());

        if($task->getcompletedAt()) {
            return $this->json(['status' => 400, 'message' => 'This task has already been marked as completed'], Response::HTTP_BAD_REQUEST);
        }

        $task->setCompletedAt(new \DateTimeImmutable('now'));

        $entityManager->flush();

        return $this->json(
            ['status' => 200, 'message' => 'Task marked as completed successfully', 'data' => $task],
            Response::HTTP_CREATED,
            [],
            [ObjectNormalizer::IGNORED_ATTRIBUTES => ['user']]
        );
    }
}
