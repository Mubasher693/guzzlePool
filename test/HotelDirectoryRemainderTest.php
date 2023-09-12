<?php
namespace test;

use PHPUnit\Framework\TestCase;
use src\HotelDirectoryRemainder;
use Dotenv\Dotenv;

class HotelDirectoryRemainderTest extends TestCase
{

    protected function setUp(): void
    {
        parent::setUp();
        $baseURL = $_ENV['BASE_DRIVE_URL'];
        $this->mockGIATADriveWithRemainder = [
            'urls' => [
                "$baseURL/9/json",
                "$baseURL/10/json",
                "$baseURL/12/json",
                "$baseURL/14/json"
            ]
        ];
        $this->mockGIATADriveWithOutRemainder = [
            'urls' => [
                "$baseURL/56/json",
                "$baseURL/68/json",
                "$baseURL/3/json",
                "$baseURL/25/json",
                "$baseURL/144/json",
            ]
        ];
        $this->mockGIATADriveWithCommainRating = [
            'urls' => [
                "$baseURL/289/json",
                "$baseURL/540/json",
                "$baseURL/769/json",
                "$baseURL/1265/json"
            ]
        ];
        $this->batchSize = 2;
    }

    /**
     * @return void
     */
    public function testCheckRemainder(): void {

        $operation = new HotelDirectoryRemainder($_ENV['DRIVE_URL'], $_ENV['BATCH_SIZE']);
        // With Remainder
        $count =  $operation->checkRemainder($this->mockGIATADriveWithRemainder, $this->batchSize);
        $this->assertEquals(0, $count, "Total $count GIATA-IDs divisible by rating without a remainder.");
        // Without Remainder
        $count =  $operation->checkRemainder($this->mockGIATADriveWithOutRemainder, $this->batchSize);
        $this->assertEquals(4, $count, "Total $count GIATA-IDs divisible by rating without a remainder.");
        // With Rating having , sign
        $count =  $operation->checkRemainder($this->mockGIATADriveWithCommainRating, $this->batchSize);
        $this->assertEquals(1, $count, "Total $count GIATA-IDs divisible by rating without a remainder.");
    }
}