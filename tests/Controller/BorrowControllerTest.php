<?php
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BorrowControllerTest extends WebTestCase
{
    // Test the borrow book functionality
    public function testBorrowBook()
    {
        $client = static::createClient();
        // Send a POST request to the /borrow endpoint with the user and book IDs
        $client->request('POST', '/api/borrows', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'userId' => 1,
            'bookId' => 1,
            'borrowDate' => '2023-07-01'
        ]));

        // Assert that the response status code is 201 (Created)
        $this->assertEquals(201, $client->getResponse()->getStatusCode());

        // Assert that the response content is JSON
        $this->assertJson($client->getResponse()->getContent());
    }

    // Test the return book functionality
    public function testReturnBook()
    {   
        $client = static::createClient();
        
        // Send a POST request to the /borrows endpoint with the borrow ID
        $client->request('PUT', '/api/borrows/1', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'returnDate' => '2023-07-10'
        ]));

        // Assert that the response status code is 200 (Created)
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        // Assert that the response content is JSON
        $this->assertJson($client->getResponse()->getContent());
    }

    // Test the view borrowing history functionality
    public function testViewBorrowingHistory()
    {
        $client = static::createClient();
        // Send a GET request to the /history endpoint
        $client->request('GET', '/api/borrows');

        // Assert that the response status code is 200 (OK)
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        // Assert that the response content is JSON
        $this->assertJson($client->getResponse()->getContent());
    }
}
?>