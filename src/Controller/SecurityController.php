<?php

namespace App\Controller;

use App\Form\RegisterType;
use App\Service\AccountService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{


    public function __construct(
        private AccountService $accountService
    )
    {
    }

    #[Route('/logowanie', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route('/register', name: 'register_request')]
    public function register(Request $request)
    {
        $login = $request->get('register')['login'];
        $password = $request->get('register')['password'];

        $this->accountService->register($login, $password);

        return new RedirectResponse('home');
    }

    #[Route('/users', name: 'get_user_request')]
    public function findUser(Request $request): JsonResponse
    {
        if ($this->getUser() === null) return new JsonResponse();

        return new JsonResponse(array(
            'username' => $this->getUser()->getUsername(),
            'roles' => $this->getUser()->getRoles()
        ));
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

}
