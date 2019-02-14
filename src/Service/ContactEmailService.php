<?php
/**
 * Created by PhpStorm.
 * User: adminHOC
 * Date: 13/02/2019
 * Time: 14:44
 */

namespace App\Service;

use App\Service\EmailService;


class ContactEmailService
{
    private $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    public function getContactEmailBody()
    {
        $email = [
            'form_params' => [
                'from' => 'email@email.fr',
                'to' => 'hoc2019@ld-web.net',
                'subject' => 'Merci du contact',
                'body' => 'Ceci est un test'
            ]
        ];

        return $this->emailService->sendEmail($email);
    }

    //'EmailTemplate/contact.html.twig';
}