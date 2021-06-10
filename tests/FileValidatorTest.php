<?php


namespace App\Tests;


use App\Service\FileValidator;
use PHPUnit\Framework\TestCase;

class FileValidatorTest extends TestCase
{

    private FileValidator $fileValidator;

    protected function setUp(): void
    {
        $this->fileValidator = new FileValidator();
    }

    public function testValidExtension() {
        $this->assertEquals(true, $this->fileValidator->isValid());
    }
}