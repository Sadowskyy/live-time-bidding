<?php


namespace App\Service;


use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class AuctionService
{

    public function __construct(
        private AccountService $accountService,
        private AuctionValidator $validator,
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
        if ($this->validator->isValid($price, $name) === false) {
            throw new \Exception('zle cos tam');
        }
        $auction->setName($name);
        $auction->setPrice($price);
        $auction->setAuthor($user);

        $this->entityManager->persist($auction);
        $this->entityManager->flush();

        return $auction;
    }

    public function delete(UserInterface $user, Product $auction): void
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
}