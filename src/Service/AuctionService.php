<?php


namespace App\Service;


use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use function Symfony\Component\Translation\t;

class AuctionService
{

    public function __construct(
        private AccountService $accountService,
        private AuctionValidator $auctionValidator,
        private ProductRepository $productRepository,
        private UserRepository $userRepository,
        private EntityManagerInterface $entityManager
    )
    {
    }

    public function getAuction(int $id): ?Product
    {
        return $this->productRepository->findOneBy(['id' => $id]);
    }

    public function create(UserInterface $user, int $price, string $name): Product
    {
        $user = $this->accountService->getUser($user->getUsername());
        $auction = new Product();

        if ($this->auctionValidator->isValid($price, $name) === false) {
            throw new \Exception('Niestety ale coś się nie zgadza');
        }
        if ($this->productRepository->findOneBy(['name'=>$name])) {
            throw new \Exception('Aukcja z taka nazwa już istnieje');
        }

        $auction->setName($name);
        $auction->setPrice($price);
        $auction->setAuthor($user);

        $this->entityManager->persist($auction);
        $this->entityManager->flush();

        return $auction;
    }

    public function delete(UserInterface $user, ?Product $auction): void
    {
        if ($auction === null) {
            throw new \Exception('Taka aukcja nie istnieje');
        }
        if ($auction->getAuthor()->getUsername() !== $user->getUsername()) {
            throw new \Exception('Nie możesz usunąc nie swojej aukcji');
        }
        $this->entityManager->remove($auction);
        $this->entityManager->flush();
    }

    public function getJsonResponse(int $auctionId): array
    {
        $auction = $this->getAuction($auctionId);

        return $this->convertToJson($auction);
    }

    public function biddAuction(int $auctionId, int $biddOffer, string $username): array
    {
        $auction = $this->getAuction($auctionId);
        $user = $this->userRepository->findOneBy(['username' => $username]);

        if (true === empty($auctionId)) {
            throw new \Exception('Taka aukcja nie istnieje');
        }
        if ($auction->getPrice() > $biddOffer || $auction->getAuthor() === $user) {
            throw new \Exception('Przykro nam ale nie możesz tego zrobić');
        }

        $auction->setPrice($biddOffer);
        $auction->setLastBidd($user);

        $this->entityManager->persist($auction);
        $this->entityManager->flush();

        return $this->convertToJson($auction);
    }

    private function convertToJson(Product $auction): array
    {
        $response = array(
            'id' => $auction->getId(),
            'name' => $auction->getName(),
            'price' => $auction->getPrice(),
            'author' => array(
                'id' => $auction->getAuthor()->getId(),
                'username' => $auction->getAuthor()->getUsername()
            ),
            'isActive' => $auction->getActive(),
            'image' => $auction->getImage()
        );
        if ($auction->getLastBidd() === null) {
            $lastBidd = array('lastBidd' => array(
                'id' => null,
                'username' => null
            ));
        } else {
            $lastBidd = array('lastBidd' => array(
                'id' => $auction->getLastBidd()->getId(),
                'username' => $auction->getLastBidd()->getUsername()
            ));
        }
        $response[] = $lastBidd;

        return $response;
    }
}
