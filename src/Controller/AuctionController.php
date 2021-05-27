<?php

namespace App\Controller;

use App\Form\AccountType;
use App\Repository\ProductRepository;
use App\Service\AuctionService;
use phpDocumentor\Reflection\Types\This;
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

    #[Route('/auctions', name: 'create_auction')]
    public function createAuction(Request $request): Response
    {
        $user = $this->getUser();
        $price = $request->get('create_auction')['price'];
        $name = $request->get('create_auction')['name'];

        $auction = $this->auctionService->create($user, $price, $name);
        $auction = $this->productRepository->find($auction->getId());

        return $this->redirect('aukcja/' . $auction->getId());
    }

    #[Route('/auctions/{auctionId}', name: 'delete_auction')]
    public function xd(Request $request, string $auctionId): Response
    {
        $auction = $this->productRepository->find((int)$auctionId);
//        if ($auction === null)
//            throw new \Exception();
        $this->auctionService->delete($this->getUser(), $auction);

        return new RedirectResponse('/aukcje');
    }
}