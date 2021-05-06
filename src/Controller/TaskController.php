<?php

namespace App\Controller;

use App\Entity\Objective;
use App\Entity\Task;
use App\Entity\User;
use App\Form\TaskType;
use App\Repository\ObjectiveRepository;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Date;

/**
 * Class TaskController
 * @package App\Controller
 * @Route("/task", name="task.")
 */
class TaskController extends AbstractController
{
    /**
     * @Route("/index", name="index")
     * @param UserRepository $userRepository
     * @param TaskRepository $taskRepository
     * @return Response
     */
    public function index(UserRepository $userRepository, TaskRepository $taskRepository): Response
    {
        $user = $userRepository->findOneBy([
            "id" => 1
        ]);
        $tasksFromDB = $taskRepository->findBy([
            "user" => $user
        ]);

        $tasks = [];

        foreach ($tasksFromDB as $task) {
            $tasks[] = [
                "name" => $task->getName(),
                "status" => $task->getStatus(),
                "id" => $task->getId(),
                "username" => $task->getUser()->getEmail(),
                "objective" => $task->getObjective()
            ];
        }
        return $this->json([
            "tasks" => $tasks
        ]);
    }

    /**
     * @Route("/find/{id}")
     * @param Task $task
     */
    public function findTaskBy(Task $task): JsonResponse
    {

        return $this->json([
            "name" => $task->getName(),
            "status" => $task->getStatus(),
            "user" => $task->getUser()->getUsername(),
            "description" => $task->getDescription(),
            "deadline_date" => $task->getDeadlineDate()
        ]);
    }

    /**
     * @Route("/create", name="create")
     * @param Request $request
     * @param UserRepository $userRepository
     * @param ObjectiveRepository $objectiveRepository
     * @return JsonResponse
     */
    public function createTask(Request $request): JsonResponse
    {
        $body = $request->getContent();
        $data = json_decode($body, true);


        $task = new Task();
        $task->setStatus(0);
        $task->setCreated(new \DateTime());
        $task->setUser($this->getUser());
        $task->setDeadlineDate(\DateTime::createFromFormat('Y-m-d', $data["deadline_date"] ));
        $form = $this->createForm(TaskType::class, $task);

        $form->submit($data);

        if ($form->isSubmitted()) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($task);
            $em->flush();

            http_response_code(200);
            return $this->json([
                "message" => "Success"
            ]);
        }
        http_response_code(400);
        return $this->json([
            "message" => "Application error"
        ]);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     * @param Task $task
     */
    public function deleteTask(Task $task): JsonResponse
    {
        if ($task) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($task);
            $em->flush();

            http_response_code(200);
            return $this->json([
                "message" => "Success"
            ]);
        }
        http_response_code(400);
        return $this->json([
            "message" => "Task invalid"
        ]);
    }

    /**
     * @Route("/update/{id}", name="update")
     * @param Task $task
     * @param Request $request
     */
    public function updateTask(Task $task, Request $request): JsonResponse
    {
        $body = $request->getContent();
        $data = json_decode($body, true);


        $form = $this->createForm(TaskType::class, $task);
        if($data === null) {
            return $this->json([
                "message" => "Application Error"
            ]);
        }
        $form->submit($data);

        if ($form->isSubmitted()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($task);
            $em->flush();


            http_response_code(200);
            return $this->json([
                "message" => "Success"
            ]);
        }

        http_response_code(400);
        return $this->json([
            "message" => "Application Error"
        ]);
    }

    /**
     * @Route("/mark/{id}", name="mark")
     * @param Request $request
     * @param TaskRepository $taskRepository
     * @return JsonResponse
     */
    public function markTask(Request $request, TaskRepository $taskRepository): JsonResponse
    {
        $id = $request->get("id");
        $task = $taskRepository->findOneBy([
            "id" => $id
        ]);

        if ($task->getStatus() === 0) {
            $task->setStatus(1);
            $em = $this->getDoctrine()->getManager();
            $em->persist($task);
            $em->flush();
            return $this->json([
                "message" => "Success"
            ]);
        } else if ($task->getStatus() === 1) {
            $task->setStatus(0);
            $em = $this->getDoctrine()->getManager();
            $em->persist($task);
            $em->flush();
            return $this->json([
                "message" => "Success"
            ]);
        }
        return $this->json([
            "message" => "Error"
        ]);


    }

    /**
     * @Route("/get/{id}", name="get")
     * @param Objective $objective
     * @param TaskRepository $taskRepository
     */
    public function getTasksByObjective(Objective $objective, TaskRepository $taskRepository) {

        $tasksFromDB = $taskRepository->findBy([
            "objective" => $objective
        ]);

        $tasks = [];
        foreach ($tasksFromDB as $task) {
            $tasks[] = [
                "name" => $task->getName(),
                "status" => $task->getStatus(),
                "id" => $task->getId(),
                "username" => $task->getUser()->getEmail(),
                "objective" => $task->getObjective()->getName()
            ];
        }
        return $this->json([
            "tasks" => $tasks
        ]);

    }
}
