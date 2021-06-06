<?php

namespace App\Controller;

use App\Form\AccountType;

use App\Form\ChangeLoginType;
use App\Form\CreateAuctionType;
use App\Form\ChangePasswordType;
use App\Form\RegisterType;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{

    public function __construct(
        private ProductRepository $auctionRepository,
        private UserRepository $userRepository,
        private EntityManagerInterface $entityManager
    )
    {
    }

    #[Route('/home', name: 'homepage')]
    public function homepage(Request $request): Response
    {
        $accountForm = $this->createForm(AccountType::class, null);
        $accountForm->handleRequest($request);

        return $this->render('main.html.twig', [
            'accountForm' => $accountForm->createView(),
            'number_of_auctions' => count($this->auctionRepository->findAll())
        ]);
    }

    #[Route('/rejestracja', name: 'register_page')]
    public function register(Request $request): Response
    {
        if ($this->getUser() !== null) return new RedirectResponse('home?error=Zaloguj się lub stwórz konto');

        $registerForm = $this->createForm(RegisterType::class, null);
        $registerForm->handleRequest($request);

        return $this->render('security/register.html.twig', [
            'registerForm' => $registerForm->createView()
        ]);
    }

    #[Route('/aukcje', name: 'auctions_page')]
    public function auctions(Request $request): Response
    {
        $user = $this->getUser();
        $auctionForm = $this->createForm(CreateAuctionType::class, null);
        $auctionForm->handleRequest($request);

        $auctions = $this->auctionRepository->findBy(['active' => true]);

        if ($auctions === null)
            throw new \Exception();
        return $this->render('auctions.html.twig', [
            'user' => $user,
            'auctions' => $auctions,
            'auctionForm' => $auctionForm->createView()
        ]);
    }

    #[Route('/aukcja/{auctionId}', name: 'auction_page')]
    public function findAuction(Request $request, string $auctionId): Response
    {
        $user = $this->getUser();

        if ($user !== null) {
            $this->userRepository->findOneBy(['username' => $user->getUsername()]);
        }
        $auction = $this->auctionRepository->findOneBy((['id' => $auctionId]));

        return $this->render('auction.html.twig', [
            'user' => $user,
            'auction' => $auction
        ]);
    }

    #[Route('/konto', name: 'user_page')]
    public function account(Request $request): Response
    {
        if ($this->getUser() === null) return new RedirectResponse('home?error=Zaloguj się lub stwórz konto');

        $user = $this->userRepository->findOneBy(['username'=> $this->getUser()->getUsername()]);

        $passwordForm = $this->createForm(ChangePasswordType::class);
        $usernameForm = $this->createForm(ChangeLoginType::class);
        $passwordForm->handleRequest($request);
        $usernameForm->handleRequest($request);

        return $this->render('user.html.twig', [
            'user' => $user,
            'passwordForm' => $passwordForm->createView(),
            'usernameForm'=> $usernameForm->createView(),
            'winningAuctions' => $user->getWinningAuctions(),
            'addedAuctions'=> $user->getAddedAuctions()
        ]);
    }
}