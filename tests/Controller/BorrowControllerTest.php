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
        $this->client->request('POST', '/api/borrows/borrowBook', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'bookId' => 1,
            'userId' => 1,
            'borrowDate' => '2024-07-25',
            'returnDate' => '2024-08-25'
        ]));

        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
    }

    public function testReturnBook()
    {
        $this->client->request('PUT', '/api/borrows/returnBook/1', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'bookId' => 1,
            'userId' => 1,
        ]));

        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
    }

    public function testViewBorrowingHistory()
    {
        $this->client->request('GET', '/api/borrows/history/1');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testBorrowBookWhenUnavailable()
    {
        $this->client->request('POST', '/api/borrows/borrowBook', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'bookId' => 2,
            'userId' => 1,
            'borrowDate' => '2024-07-25',
            'returnDate' => '2024-08-25'
        ]));

        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
    }
}

?>