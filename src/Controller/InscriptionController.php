<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\InscriptionType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
        $content = $request->getContent();
        $data = json_decode($request->getContent($content), true);
        $em = $this->getDoctrine()->getManager();
        $user= new User();
        $form = $this->createForm(InscriptionType::class, $user);

        $form->handleRequest($request);

        $form->submit($data);



        if ($form->isSubmitted() && $form->isValid()) {

            $user = $form->getData();
            $em->persist($user);
            $em->flush();

        }
        $data = $this->get('serializer')->serialize($user, 'json');

        return new JsonResponse($data, 200, [], true);
    }
}
