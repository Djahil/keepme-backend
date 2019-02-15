<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Security\JwtTokenAuthenticator;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Utils\Validator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("user")
 */
class UserController extends AbstractController
{

    /**
     * @Route("/add", name="user_add", methods={"PUT","POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function addNewUser(Request $request,  UserPasswordEncoderInterface $passwordEncoder,  Validator $validator )
    {

        $entityManager = $this->getDoctrine()->getManager();

        $email = $request->request->get('email');
        $roles[] = $request->request->get('roles');
        $password = $request->request->get('password');
        $nom = $request->request->get('nom');
        $prenom = $request->request->get('prenom');
        $nom_entreprise = $request->request->get('nom_entreprise');
        $logo = $request->request->get('logo');
        $adresse = $request->request->get('adresse');
        $code_postal = $request->request->get('code_postal');
        $ville = $request->request->get('ville');
        $site_web = $request->request->get('site_web');
        $social = $request->request->get('social');

        $validator->validateEmail($email);
        $validator->validatePassword($password);


        $user = new User();

        $user->setEmail($email);
        $user->setRoles($roles);
        $encodedPassword = $passwordEncoder->encodePassword($user, $password);
        $user->setPassword($encodedPassword);
        $user->setNom($nom);
        $user->setPrenom($prenom);
        $user->setNomEntreprise($nom_entreprise);
        $user->setLogo($logo);
        $user->setAdresse($adresse);
        $user->setCodePostal($code_postal);
        $user->setVille($ville);
        $user->setSiteWeb($site_web);
        $user->setSocial($social);

        $entityManager->persist($user);
        $entityManager->flush();

        $data = $this->get('serializer')->serialize($user, 'json');

        return new JsonResponse($data, 200, [], true);

    }


//    /**
//     * @Route("/login", name="user_login", methods={"PUT","POST"})
//     * @param Request $request
//     * @return JsonResponse
//     */
//    public function loginAction( Request $request, JWTEncoderInterface $jwtEncoder, EntityManagerInterface $em, JwtTokenAuthenticator $token)
//
//    {
//
//        $token->getCredentials($request);
//
//        $data = $this->get('serializer')->serialize($token, 'json');
//
//        return new JsonResponse($data, 200, [], true);
//
//    }



}
