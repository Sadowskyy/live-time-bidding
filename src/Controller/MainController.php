<?php

namespace App\Controller;

use App\Form\AccountType;
use App\Form\AuctionType;
use App\Form\CreateAuctionType;
use App\Form\RegisterType;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{

    public function __construct(
        private ProductRepository $productRepository,
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
            'number_of_auctions' => 0
        ]);
    }

    #[Route('/rejestracja', name: 'register_page')]
    public function register(Request $request): Response
    {
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

        $auctions = $this->productRepository->findBy(['active' => true]);

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
        $user = $this->userRepository->findOneBy(['username' => $this->getUser()->getUsername()]);
        $auction = $this->productRepository->findOneBy((['id' => $auctionId]));


        return $this->render('auction.html.twig', [
            'user' => $user,
            'auction' => $auction
        ]);
    }
}
