<?php


namespace App\Service;


use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

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

    public function changePassword(string $newPassword, string $login)
    {
        $user = $this->getUser($login);

        if ($this->passwordValidator->isValid($newPassword) === false) {
            throw new \Exception('Hasło nie spełnia wymagań');
        }
        if ($this->encoder->isPasswordValid($user, $newPassword) === true) {
            throw new \Exception('Nie możesz użyc tego samego hasła');
        }

        $user->setPassword($this->encoder->encodePassword($user, $newPassword));

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    public function changeLogin(string $login, string $password, UserInterface $user)
    {
        $user = $this->getUser($user->getUsername());

        if ($login === $user->getUsername() && strlen($login) < 6) {
            throw new \Exception('Nie możesz zmienić na taki login.');
        }
        if ($this->encoder->isPasswordValid($user, $password) === false) {
            throw new \Exception('Nie poprawne hasło.');
        }
        $user->setUsername($login);

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}