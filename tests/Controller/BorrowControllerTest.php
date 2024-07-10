<?php
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BorrowControllerTest extends WebTestCase
{
    public function testBorrowBook()
    {
        $client = static::createClient();
        $client->request('POST', '/api/borrows', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'userId' => 1,
            'bookId' => 1,
            'borrowDate' => '2023-07-01'
        ]));

        $this->assertEquals(201, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());
    }

    public function testReturnBook()
    {
        $client = static::createClient();
        $client->request('PUT', '/api/borrows/1', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'returnDate' => '2023-07-10'
        ]));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());
    }

    public function testViewBorrowingHistory()
    {
        $client = static::createClient();
        $client->request('GET', '/api/borrows');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());
    }
}
?>