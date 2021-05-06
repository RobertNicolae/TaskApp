<?php

namespace App\Controller;

use App\Entity\Objective;
use App\Form\ObjectiveType;
use App\Repository\ObjectiveRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/objective", name="objective.")
 * Class ObjectiveController
 * @package App\Controller
 */
class ObjectiveController extends AbstractController
{
    /**
     * @Route("/", name="index")
     * @param ObjectiveRepository $objectiveRepository
     * @param UserRepository $userRepository
     * @return Response
     */
    public function index(ObjectiveRepository $objectiveRepository, UserRepository $userRepository): Response
    {
        $headers = getallheaders();
        if (!isset($headers["X-AUTH-TOKEN"])) {
            http_response_code(400);
            echo json_encode([
                "message" => "Bad credentials"
            ]);
        }

        $user = $this->getUser();

        $objectivesFromDB = $objectiveRepository->findBy([
            "user" => $user
        ]);
        $objectives = [];


        foreach ($objectivesFromDB as $objective) {
            $objectives[] = [
                "name" => $objective->getName(),
                "id" => $objective->getId(),
                "status" => $objective->getStatus(),
                "username" => $objective->getUser()->getEmail()
            ];



        }
        return $this->json([
            "objectives" => $objectives
        ]);
    }

    /**
     * @Route("/create", name="create")
     * @param Request $request
     * @param UserRepository $userRepository
     * @return JsonResponse
     */
    public function createObjective(Request $request, UserRepository $userRepository): JsonResponse
    {
        $body = $request->getContent();
        $data = json_decode($body, true);

        $objective = new Objective();

        $objective->setUser($this->getUser());
        $objective->setStatus(0);
        $form = $this->createForm(ObjectiveType::class, $objective);
        $form->submit($data);

        if ($form->isSubmitted()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($objective);
            $em->flush();

            return $this->json([
                "message" => "Success"
            ]);
        }
        return $this->json([
            "message" => "Application error"
        ]);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     * @param Objective $objective
     * @return JsonResponse
     */
    public function deleteObjective(Objective $objective): JsonResponse
    {
        if ($objective) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($objective);
            $em->flush();

            http_response_code(200);
            return $this->json([
                "message" => "Success"
            ]);
        }
        http_response_code(400);
        return $this->json([
            "message" => "Invalid Objective"
        ]);
    }

    /**
     * @Route("/mark/{id}", name="mark")
     * @param Objective $objective
     */
    public function mark(Objective $objective)
    {
        if ($objective->getStatus() === 0) {
            $objective->setStatus(1);
            $em = $this->getDoctrine()->getManager();
            $em->persist($objective);
            $em->flush();
            return $this->json([
                "status" => $objective->getStatus()
            ]);
        } else if ($objective->getStatus() === 1){
            $objective->setStatus(0);
            $em = $this->getDoctrine()->getManager();
            $em->persist($objective);
            $em->flush();
            return $this->json([
                "message" => "Success"
            ]);
        }
        return $this->json([
            "message" => "Error"
        ]);

    }

}
