<?php

namespace App\Controller;

use App\Form\ConnexionType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class Connexion extends AbstractController
{
    /**
     * @Route("/connexion", name="connexion")
     */
    public function connexion (Request $request): Response
    {
       $connexion = new Connexion();
       $form = $this->createForm(ConnexionType::class, $connexion);

       $form->handleRequest($request);

       if ($form->isSubmitted() && $form->isValid()) {

           $form->getData();
       }
       return new Response($form);
    }
}



