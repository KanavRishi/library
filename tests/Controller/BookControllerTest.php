<?php
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BookControllerTest extends WebTestCase
{
    // Test case to add a new book via API
    public function testAddNewBook()
    {
        $client = static::createClient();
        // Send a POST request to create a new book
        $client->request('POST', '/api/books', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'title' => 'Test Book',
            'author' => 'Test Author',
            'genre' => 'Fiction',
            'isbn' => '1234567890',
            'publishedDate' => '2023-07-01',
            'status' => 'Available'
        ]));

        // Assert the HTTP status code is 201 (Created)
        $this->assertEquals(201, $client->getResponse()->getStatusCode());
        // Assert the response content is JSON
        $this->assertJson($client->getResponse()->getContent());
    }

    // Test case to view the list of books via API
    public function testViewListOfBooks()
    {
        $client = static::createClient();
        // Send a GET request to fetch the list of books
        $client->request('GET', '/api/books');

        // Assert the HTTP status code is 200 (OK)
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        // Assert the response content is JSON
        $this->assertJson($client->getResponse()->getContent());
    }

    // Test case to view details of a specific book via API
    public function testViewBookDetails()
    {
        $client = static::createClient();
        // Send a GET request to fetch details of a book with ID 1
        $client->request('GET', '/api/books/1');

        // Assert the HTTP status code is 200 (OK)
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        // Assert the response content is JSON
        $this->assertJson($client->getResponse()->getContent());
    }

    // Test case to edit an existing book via API
    public function testEditBook()
    {
        $client = static::createClient();
        // Send a PUT request to update details of the book with ID 1
        $client->request('PUT', '/api/books/1', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'title' => 'Updated Test Book',
            'author' => 'Updated Test Author',
            'genre' => 'Non-Fiction',
            'isbn' => '0987654321',
            'publishedDate' => '2023-07-02',
            'status' => 'Available'
        ]));

        // Assert the HTTP status code is 200 (OK)
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        // Assert the response content is JSON
        $this->assertJson($client->getResponse()->getContent());
    }

    // Test case to remove an existing book via API
    public function testRemoveBook()
    {
        $client = static::createClient();
        // Send a DELETE request to remove the book with ID 1
        $client->request('DELETE', '/api/books/1');

        // Assert the HTTP status code is 204 (No Content)
        $this->assertEquals(204, $client->getResponse()->getStatusCode());
    }
}
?>
