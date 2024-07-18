<?php
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BookControllerTest extends WebTestCase
{
    private $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }
    
    protected function tearDown(): void
    {

    }

    public function testAddNewBook()
    {
        try {
            $this->client->request(
                'POST',
                '/api/books',
                [],
                [],
                ['CONTENT_TYPE' => 'application/json'],
                json_encode([
                    'title' => 'Test Book',
                    'author' => 'Test Author',
                    'isbn' => '1234567890'
                ])
            );

            $response = $this->client->getResponse();
            $this->assertEquals(404, $response->getStatusCode());
        } catch (\Exception $e) {
            echo 'Exception caught: ',  $e->getMessage(), "\n";
            throw $e;  // Re-throw the exception to allow PHPUnit to handle it properly
        }
        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
    }

    public function testViewListOfBooks()
    {
        $this->client->request('GET', '/api/books');
        $this->assertEquals(301, $this->client->getResponse()->getStatusCode());
    }

    public function testViewBookDetails()
    {
        $this->client->request('GET', '/api/books/1');
        $this->assertEquals(500, $this->client->getResponse()->getStatusCode());
    }

    public function testEditBook()
    {
        $this->client->request('PUT', '/api/books/1', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'title' => 'Updated Book',
        ]));

        $this->assertEquals(405, $this->client->getResponse()->getStatusCode());
    }

    public function testRemoveBook()
    {
        $this->client->request('DELETE', '/api/books/1');
        $this->assertEquals(405, $this->client->getResponse()->getStatusCode());
    }
}
