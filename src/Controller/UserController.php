<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\InscriptionType;
use App\Service\EmailService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/user")
 * Class InscriptionController
 * @package App\Controller
 */
class UserController extends AbstractController
{
    /**
     * @Route("/add", name="user_add", methods={"POST"})
     * @param Request $request
     * @return Response
     */
    public function addUser (Request $request, EmailService $mailService, UserPasswordEncoderInterface $encoder): Response
    {
        $user     = new User();
        $form     = $this->createForm(InscriptionType::class, $user);
        $content  = $request->getContent();
        $data     = json_decode($content, true);
        $em       = $this->getDoctrine()->getManager();
        $encoded  = $encoder->encodePassword($user, $data['password']);
        
        // On catch l'erreur si il y'en a une
        try {
            $form->submit($data);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'ça ne marche pas']);
        }

        // Si le formulaire et submit et valide tu me l'envoi en base de donnée
        if ($form->isSubmitted() && $form->isValid())
        {
            $user->setRoles(['ROLE_USER']);
            $user->setPassword($encoded);

            $em->persist($user);
            $em->flush();

            $this->sendInscriptionConfirmation($user, $mailService);
        }
        $data = $this->get('serializer')->serialize($user, 'json');

        return new JsonResponse($data, 200, [], true);
    }

    protected function sendInscriptionConfirmation(User $data, EmailService $emailService)
    {
        $body = $this->renderView('EmailTemplate/inscription.html.twig', [
            'prenom' => $data->getPrenom()
        ]);

        $userMailData =
            [
                "from" => "hoc2019@ld-web.net",
                "to" => $data->getEmail(),
                "subject" => "Bienvenue sur KeepMe !",
                "body" => $body,
            ];

        return new Response($emailService->sendEmail($userMailData));
    }
}