<?php
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BookControllerTest extends WebTestCase
{
    public function testAddNewBook()
    {
        $client = static::createClient();
        $client->request('POST', '/api/books', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'title' => 'Test Book',
            'author' => 'Test Author',
            'genre' => 'Fiction',
            'isbn' => '1234567890',
            'publishedDate' => '2023-07-01',
            'status' => 'Available'
        ]));

        $this->assertEquals(201, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());
    }

    public function testViewListOfBooks()
    {
        $client = static::createClient();
        $client->request('GET', '/api/books');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());
    }

    public function testViewBookDetails()
    {
        $client = static::createClient();
        $client->request('GET', '/api/books/1');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());
    }

    public function testEditBook()
    {
        $client = static::createClient();
        $client->request('PUT', '/api/books/1', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'title' => 'Updated Test Book',
            'author' => 'Updated Test Author',
            'genre' => 'Non-Fiction',
            'isbn' => '0987654321',
            'publishedDate' => '2023-07-02',
            'status' => 'Available'
        ]));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());
    }

    public function testRemoveBook()
    {
        $client = static::createClient();
        $client->request('DELETE', '/api/books/1');

        $this->assertEquals(204, $client->getResponse()->getStatusCode());
    }
}
?>