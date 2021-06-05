<?php


namespace App\Tests;


use App\Service\AuctionValidator;
use PHPUnit\Framework\TestCase;

class AuctionValidatorTest extends TestCase
{

    private AuctionValidator $auctionValidator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->auctionValidator = new AuctionValidator();
    }

    public function testAuctionWithValidNameAndWithoutValidPrice()
    {
        $this->assertEquals($this->auctionValidator->isValid(-1, 'Rower dla dzieci'), false);
    }

    public function testAuctionWithoutValidNameAndWithValidPrice()
    {
        $this->assertEquals($this->auctionValidator->isValid(1000, 'fail'), false);;
    }

    public function testAuctionWithValidParams()
    {
        $this->assertEquals($this->auctionValidator->isValid(1000, 'Rower dla dzieci'), true);
    }
}