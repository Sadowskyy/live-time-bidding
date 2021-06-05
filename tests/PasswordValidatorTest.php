<?php


namespace App\Tests;


use App\Entity\User;
use App\Service\PasswordValidator;
use PHPUnit\Framework\TestCase;

class PasswordValidatorTest extends TestCase
{

    private PasswordValidator $passwordValidator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->passwordValidator = new PasswordValidator();
    }

    public function testCorrectPassword()
    {
        $this->assertEquals($this->passwordValidator->isValid('CorrectPass'), true);
    }

    public function testIncorrectPassword()
    {
        $this->assertEquals($this->passwordValidator->isValid('incorr'), false);
    }
}