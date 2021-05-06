<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="register")
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $body = $request->getContent();

        $user = json_decode($body, true);

        $form = $this->createFormBuilder()
            ->add('email')
            ->add('password', PasswordType::class, [
                'required' => true,
            ])
            ->getForm();


        $form->submit($user);

        if($form->isSubmitted()) {
            $data = $form->getData();
            $user = new User();
            $user->setApiToken(sha1($data["email"]));
            $user->setEmail($data["email"]);
            $user->setPassword($passwordEncoder->encodePassword($user, $data["password"]));



            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            return $this->json([
                "message" => "Success"
            ]);
        }
        return $this->json([
            "message" => "incorrect data"
        ]);
    }
}
