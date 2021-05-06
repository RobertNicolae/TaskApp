<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityApiController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     * @param AuthenticationUtils $authenticationUtils
     * @param Request $request
     * @param UserRepository $userRepository
     * @return Response
     */
    public function login(UserPasswordEncoderInterface $passwordEncoder, Request $request, UserRepository $userRepository): Response
    {

        $body = $request->getContent();
        $user = json_decode($body, true);
        $user2 = $userRepository->findOneBy([
            "email" => $user["username"],
        ]);
        $pass = $passwordEncoder->isPasswordValid($user2, $user["password"]);
        if ($pass === false) {
            return $this->json([
                "message" => "incorrect password"
            ]);
        }

        if ($user2 !== NULL) {
            $token = $user2->getApiToken();
            return $this->json([
                "token" => $token
            ]);
        } else {
            http_response_code(401);
            echo json_encode([
                "message" => "Incorrect user"
            ]);
            die();
        }

//
//        // get the login error if there is one
//        $error = $authenticationUtils->getLastAuthenticationError();
//        // last username entered by the user
//        $lastUsername = $authenticationUtils->getLastUsername();
//
//        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
