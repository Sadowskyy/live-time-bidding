<?php


namespace App\Tests;


use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\AccountService;
use App\Service\PasswordValidator;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use TypeError;

class AccountServiceTest extends KernelTestCase
{


    private AccountService $accountService;

    private UserRepository $userRepository;

    private PasswordValidator $passwordValidator;

    private EntityManager $entityManager;

    private UserPasswordEncoderInterface $encoder;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
        $this->user = new User();
        $this->user->setUsername('TestUser');
        $this->user->setPassword('TestPassword');

        $this->userRepository = $this
            ->getMockBuilder(UserRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->passwordValidator = $this
            ->getMockBuilder(PasswordValidator::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->encoder = $this
            ->getMockBuilder(UserPasswordEncoderInterface::class)
            ->getMock();

        $this->entityManager = $this
            ->getMockBuilder(EntityManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->entityManager->expects($this->any())
            ->method('getRepository')
            ->willReturn($this->userRepository);


        $this->accountService = new AccountService($this->userRepository,
            $this->passwordValidator,
            $this->entityManager,
            $this->encoder);

    }

    public function testGetUserWhichIsStoredInDatabase()
    {
        $this->userRepository->expects($this->any())
            ->method('findOneBy')
            ->willReturn($this->user);

        $this->assertEquals($this->user, $this->accountService->getUser('TestUser'));
    }

    public function testGetUserWhichIsNotStoredInDatabase()
    {
        $this->userRepository->expects($this->any())
            ->method('findOneBy')
            ->willReturn(null);

        $this->assertEquals(null, $this->accountService->getUser('TestUser'));
    }

    public function testAccountRegisterWithValidPasswordAndLogin()
    {
        $this->userRepository->expects($this->any())
            ->method('findOneBy')
            ->willReturn(null);
        $this->passwordValidator->expects($this->any())
            ->method('isValid')
            ->willReturn(true);
        $this->encoder->expects($this->any())
            ->method('encodePassword')
            ->willReturn(random_bytes(5));

        $this->doesNotPerformAssertions();
        $this->accountService->register($this->user->getUsername(), 'xd');
    }

    public function testAccountRegisterWithNotValidLogin()
    {
        $this->userRepository->expects($this->any())
            ->method('findOneBy')
            ->willReturn($this->user);
        $this->passwordValidator->expects($this->any())
            ->method('isValid')
            ->willReturn(true);

        $this->expectException(\Exception::class);
        $this->accountService->register('TestUser', 'password123');
    }

    public function testAccountRegisterWithNotValidPassword()
    {
        $this->userRepository->expects($this->any())
            ->method('findOneBy')
            ->willReturn($this->user);
        $this->passwordValidator->expects($this->any())
            ->method('isValid')
            ->willReturn(false);

        $this->expectException(\Exception::class);
        $this->accountService->register('TestUser', 'password123');
    }


    public function testChangePasswordWithValidUserAndPassword()
    {
        $this->userRepository->expects($this->any())
            ->method('findOneBy')
            ->willReturn($this->user);
        $this->passwordValidator->expects($this->any())
            ->method('isValid')
            ->willReturn(true);
        $this->encoder->expects($this->any())
            ->method('isPasswordValid')
            ->willReturn(false);
        $this->encoder->expects($this->any())
            ->method('encodePassword')
            ->willReturn(random_bytes(5));

        $this->doesNotPerformAssertions();
        $this->accountService->changePassword('NewPassword', $this->user->getUsername());
    }

    public function testChangePasswordWithNotValidPassword()
    {
        $this->userRepository->expects($this->any())
            ->method('findOneBy')
            ->willReturn($this->user);
        $this->passwordValidator->expects($this->any())
            ->method('isValid')
            ->willReturn(false);
        $this->encoder->expects($this->any())
            ->method('isPasswordValid')
            ->willReturn(false);

        $this->expectException(\Exception::class);
        $this->accountService->changePassword('NewPassword', $this->user->getUsername());
    }

    public function testChangePasswordWithSamePasswordAsUserPassword()
    {
        $this->userRepository->expects($this->any())
            ->method('findOneBy')
            ->willReturn($this->user);
        $this->passwordValidator->expects($this->any())
            ->method('isValid')
            ->willReturn(true);
        $this->encoder->expects($this->any())
            ->method('isPasswordValid')
            ->willReturn(true);

        $this->expectException(\Exception::class);
        $this->accountService->changePassword('NewPassword', $this->user->getUsername());
    }

    public function testChangePasswordWithUnexistingUser()
    {
        $this->userRepository->expects($this->any())
            ->method('findOneBy')
            ->willReturn(null);
        $this->passwordValidator->expects($this->any())
            ->method('isValid')
            ->willReturn(true);
        $this->encoder->expects($this->any())
            ->method('isPasswordValid')
            ->willReturn(true);

        $this->expectException(TypeError::class);
        $this->accountService->changePassword('NewPassword', $this->user->getUsername());
    }

    public function testChangeLoginWithValidUserAndPassword()
    {
        $this->userRepository->expects($this->any())
            ->method('findOneBy')
            ->willReturn($this->user);
        $this->passwordValidator->expects($this->any())
            ->method('isValid')
            ->willReturn(true);


        $this->doesNotPerformAssertions();
        $this->accountService->changeLogin('NewLogin', $this->user->getPassword(), $this->user);
    }

    public function testChangeLoginWithNotExistingUser()
    {
        $this->userRepository->expects($this->any())
            ->method('findOneBy')
            ->willReturn(null);
        $this->passwordValidator->expects($this->any())
            ->method('isValid')
            ->willReturn(false);


        $this->expectException(\Error::class);
        $this->accountService->changeLogin('NewLogin', $this->user->getPassword(), $this->user);
    }
}