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
     * @Route("/contact/mail", name="contact_mail", methods={"POST"})
     */
    public function sendReceiptFromContact(Request $request, EmailService $emailService)
    {
        $to = null;
        $prenom = null;
        $data=[];
        $formValues = json_decode($request->getContent(), true);
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);

        $form->handleRequest($request);
        $form->submit($formValues);

        if ($form->isSubmitted() && $form->isValid()) {

            $prenom = $form->get('prenom')->getData();

            $body = $this->renderView('EmailTemplate/contact.html.twig', [
                'prenom' => $prenom
            ]);

            $data =
                [
                    "from" => "hoc2019@ld-web.net",
                    "to" => $form->get('email')->getData(),
                    "subject" => "Merci de nous avoir contacté !",
                    "body" => $body,
                ];

        }

        return new Response($emailService->sendEmail($data));
    }

}
