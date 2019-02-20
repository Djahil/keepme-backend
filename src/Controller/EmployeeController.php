<?php

namespace App\Controller;

use App\Entity\Employee;
use App\Entity\User;
use App\Form\EmployeeType;
use App\Repository\EmployeeRepository;
use App\Repository\UserRepository;
use App\Service\EmailService;
use PhpParser\Error;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\DependencyInjection\Tests\Compiler\E;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Utils\Validator;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/employee")
 */
class EmployeeController extends AbstractController
{
    private $userRepository;
    //private $security;

    public function  __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
        //$this->security = $security;
    }

    /**
     * @Route("/add", name="employee_add", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function add (Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $content = $request->getContent();

        $data = json_decode($content, true);
        $user = $this->getUser();
    
        if (
            !$user instanceof User)
        {
            throw new Error('No User found');
        }


        $employee = new Employee();
        $form = $this->createForm(EmployeeType::class, $employee);

        $form->handleRequest($request);

        try{
            $form->submit($data);
        }catch (\Exception $e){
            throw new Exception($e->getMessage(), 500);
        }


        if ($form->isSubmitted() && $form->isValid()) {

            $employee = $form->getData();
            $employee->setUser($user);
            $em->persist($employee);
            $em->flush();

        }
        $data = $this->get('serializer')->serialize($employee, 'json');

        return new JsonResponse($data, 200, [], true);
        
    }

    /**
     * @Route("/show", name="employee_show", methods={"GET"})
     * @param Request $request
     * @return JsonResponse
     */
    public function getEmployeeBySlug (Request $request, EmployeeRepository $employeeRepository)
    {

        $content = $request->getContent();

        $myData = json_decode($content, true);

        $employee = $employeeRepository->findBy($myData);


        $data = $this->get('serializer')->serialize($employee, 'json');

        return new JsonResponse($data, 200, [], true);
    }

    /**
     * @Route("/list", name="employee_list", methods={"GET"})
     * @param Request $request
     * @return JsonResponse
     */
    public function getEmployeesList (EmployeeRepository $employeeRepository)
    {

        $employees = $employeeRepository->findAll();


        $data = $this->get('serializer')->serialize($employees, 'json');

        return new JsonResponse($data, 200, [], true);
    }

    /**
     * @Route("/delete", name="employee_delete", methods={"DELETE"})
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function deleteEmployee (Request $request, EmployeeRepository $employeeRepository)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $content = $request->getContent();

        $myData = json_decode($content, true);

        $employee = $employeeRepository->findOneBy($myData);

        $entityManager->remove($employee);
        $entityManager->flush();


        $data = $this->get('serializer')->serialize('employee deleted', 'json');

        return new JsonResponse($data, 200, [], true);
    }


    /**
     * @Route("/update", name="employee_update", methods={"PUT"})
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function updateEmployee (Request $request, EmployeeRepository $employeeRepository)
    {
        $content = $request->getContent();
        $entityManager = $this->getDoctrine()->getManager();

        $myData = json_decode($content, true);

        $employee = $employeeRepository->findOneBy($myData[]['id']);

        $employee->setNom($myData['nom']);
        $employee->setPrenom($myData['prenom']);
        $employee->setEmail($myData['email']);
        $employee->setPoste($myData['poste']);
        $employee->setTelephone($myData['telephone']);
        $entityManager->persist($employee);
        $entityManager->flush();

        $data = $this->get('serializer')->serialize($employee, 'json');

        return new JsonResponse($data, 200, [], true);

    }

    /**
     * @Route("/sendcard", name="employee_send_card", methods={"POST"})
     * @param Request $request
     * @param EmailService $emailService
     * @param EmployeeRepository $employeeRepository
     * @return JsonResponse|Response
     */
    public function sendCardByMail(Request $request, EmailService $emailService, EmployeeRepository $employeeRepository)
    {
        $formValues = json_decode($request->getContent(), true);
        $slug = $formValues['slug'];
        $employee = $employeeRepository->findOneBySlug($slug);
        /* $form = $this->createForm(EmployeeType::class, $employee);
        $form->handleRequest($request);
        $form->submit($formValues);*/

        if ($employee != null)
        {
            $body = $this->renderView('EmailTemplate/carteVisite.html.twig', [
                'employeePrenom' => $employee->getPrenom(),
                'userPrenom' => $employee->getUser()->getPrenom(),
                'userNom' => $employee->getUser()->getNom(),
                'slug' => "http://localhost:3000/card/".$employee->getSlug()
            ]);

            $userMailData =
                [
                    "from" => "hoc2019@ld-web.net",
                    "to" => $employee->getEmail(),
                    "subject" => "KeepMe : votre carte de visite numérique",
                    "body" => $body,
                ];
            return new Response($emailService->sendEmail($userMailData));
        }
        else
        {
            return new Response("Cet(te) employé(e) n'existe pas");
        }
    }
}
