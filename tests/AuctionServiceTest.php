<?php


namespace App\Tests;


use App\Entity\Product;
use App\Entity\User;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use App\Service\AccountService;
use App\Service\AuctionService;
use App\Service\AuctionValidator;
use App\Service\PasswordValidator;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AuctionServiceTest extends KernelTestCase
{

    private AccountService $accountService;

    private AuctionService $auctionService;

    private AuctionValidator $auctionValidator;

    private ProductRepository $productRepository;

    private UserRepository $userRepository;

    private EntityManagerInterface $entityManager;

    private UserPasswordEncoderInterface $encoder;

    private PasswordValidator $passwordValidator;

    private Product $auction;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();

        $this->auction = new Product();
        $this->user = new User();
        $this->user->setUsername('Anonimowysprzedawca');
        $this->auction->setName('Rowerek123');
        $this->auction->setPrice(1200);
        $this->auction->setAuthor($this->user);


        $this->userRepository = $this
            ->getMockBuilder(UserRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->productRepository = $this
            ->getMockBuilder(ProductRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->entityManager = $this
            ->getMockBuilder(EntityManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->userRepository = $this
            ->getMockBuilder(UserRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->auctionValidator = $this
            ->getMockBuilder(AuctionValidator::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->passwordValidator = $this
            ->getMockBuilder(PasswordValidator::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->encoder = $this
            ->getMockBuilder(UserPasswordEncoderInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->accountService = new AccountService($this->userRepository, $this->passwordValidator,
            $this->entityManager, $this->encoder);

        $this->auctionService = new AuctionService($this->accountService, $this->auctionValidator,
            $this->productRepository, $this->userRepository, $this->entityManager);
    }

    public function testGetAuctionWhichIsStoredInDatabase()
    {
        $this->productRepository->expects($this->any())
            ->method('findOneBy')
            ->willReturn($this->auction);

        $this->assertEquals($this->auction, $this->auctionService->getAuction(random_int(1, 100)));
    }

    public function testGetAuctionWhichIsNotStoredInDatabase()
    {
        $this->productRepository->expects($this->any())
            ->method('findOneBy')
            ->willReturn(null);

        $this->assertEquals(null, $this->auctionService->getAuction(random_int(1, 100)));
    }

    public function testDeleteExistingProduct()
    {
        $this->doesNotPerformAssertions();
        $this->auctionService->delete($this->user, $this->auction);
    }

    public function testDeleteNotExistingProduct()
    {
        $this->expectException(\Exception::class);
        $this->auctionService->delete($this->user, null);
    }

}