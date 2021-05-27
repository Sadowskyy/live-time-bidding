<?php


namespace App\Service;


use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AccountService
{
    public function __construct(
        private UserRepository $userRepository,
        private PasswordValidator $passwordValidator,
        private EntityManagerInterface $entityManager,
        private UserPasswordEncoderInterface $encoder
    )
    {
    }

    public function getUser(string $username): ?User
    {
        return $this->userRepository->findOneBy(['username' => $username]);
    }

    public function register(string $login, string $password): void
    {
        if (false === empty($this->getUser($login))) {
            throw new \Exception('Takie konto już istnieje');
        }
        $user = new User();
        $user->setUsername($login);
        $user->setRoles(array('ROLE_USER'));
        if ($this->passwordValidator->isValid($password) === true) {
            $user->setPassword($this->encoder->encodePassword($user, $password));
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}