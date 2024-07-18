<?php
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BorrowControllerTest extends WebTestCase
{
    private $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testBorrowBook()
    {
        $this->client->request('POST', '/api/borrow', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'bookId' => 1,
            'userId' => 1,
        ]));

        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
    }

    public function testReturnBook()
    {
        $this->client->request('POST', '/api/return', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'bookId' => 1,
            'userId' => 1,
        ]));

        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
    }

    public function testViewBorrowingHistory()
    {
        $this->client->request('GET', '/api/borrow/history/1');
        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
    }

    public function testBorrowBookWhenUnavailable()
    {
        $this->client->request('POST', '/api/borrow', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'bookId' => 2,
            'userId' => 1,
        ]));

        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
    }
}

?>