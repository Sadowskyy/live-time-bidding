<?php

namespace App\Controller;

use App\Form\AccountType;
use App\Repository\ProductRepository;
use App\Service\AuctionService;
use phpDocumentor\Reflection\Types\This;
use PHPUnit\Util\Json;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AuctionController extends AbstractController
{


    public function __construct(
        private AuctionService $auctionService,
        private ProductRepository $productRepository
    )
    {
    }

    #[Route('/auctions', name: 'create_auction', methods: ['POST'])]
    public function createAuction(Request $request): Response
    {
        $user = $this->getUser();
        $price = $request->get('create_auction')['price'];
        $name = $request->get('create_auction')['name'];
        $image = $_FILES['create_auction'];
        $uploadDirectory = $this->getParameter('brochures_directory');


        $auction = $this->auctionService->create($user, (int)$price, $name, $image, $uploadDirectory);

        return $this->redirect('aukcja/' . $auction->getId());
    }

    #[Route('/auctions/{auctionId}/delete', name: 'delete_auction', methods: ['DELETE'])]
    public function removeAuction(Request $request, string $auctionId): Response
    {
        $auction = $this->productRepository->find((int)$auctionId);
        $this->auctionService->delete($this->getUser(), $auction);

        return new RedirectResponse('/aukcje');
    }

    #[Route('/auctions/{auctionId}', name: 'find_auction', methods: ['GET'])]
    public function findAuction(Request $request, string $auctionId)
    {
        $auction = $this->auctionService->getJsonResponse((int)$auctionId);

        return new JsonResponse($auction);
    }
}