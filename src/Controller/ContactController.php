<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\EmailService;

class ContactController extends AbstractController
{
    /**
     * @param Request $request
     * @return mixed
     * @return Response
     * @Route("/contactmail", name="contact_mail")
     */
    public function sendReceiptFromContact(Request $request, EmailService $emailService)
    {
        $email = "";
        $prenom = null;
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $prenom = $form->get('prenom')->getData();

            $body = $this->render('EmailTemplate/contact.html.twig', [
                'prenom' => $prenom
            ]);

            $email = [
                'form_params' => [
                    'from' => 'hoc2019@ld-web.net',
                    'to' => $form->get('email')->getData(),
                    'subject' => 'Merci de nous avoir contacté !',
                    'body' => $body,
                ]
            ];

        }

        return new Response($emailService->sendEmail($email));
    }

}
