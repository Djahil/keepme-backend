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

    public function getContactEmailBody(String $email, String $body)
    {
        $email = [
            'form_params' => [
                'from' => 'hoc2019@ld-web.net',
                'to' => $email,
                'subject' => 'Merci de nous avoir contacté !',
                'body' => $body
            ]
        ];

        return $this->emailService->sendEmail($email);
    }

    //'EmailTemplate/contact.html.twig';
}