<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\InscriptionType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InscriptionController extends AbstractController
{
    /**
     * @Route("/inscription", name="inscription", methods={"POST"})
     * @param Request $request
     * @return Response
     */
    public function inscription (Request $request): Response
    {
        $user    = new User();
        $form    = $this->createForm(InscriptionType::class, $user);
        $content = $request->getContent();
        $data    = json_decode($content, true);
        $em      = $this->getDoctrine()->getManager();

        // On catch l'erreur si il y'en a une


        try {
            $form->submit($data);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'ça ne marche pas']);
        }

        // Si le formulaire et submit et valide tu me l'envoi en base de donnée
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setLogo('faresse.png');
            $user->setRoles(['ROLE_USER']);
            $em->persist($user);
            $em->flush();
        }
        $data = $this->get('serializer')->serialize($user, 'json');

        return new JsonResponse($data, 200, [], true);
    }
}
