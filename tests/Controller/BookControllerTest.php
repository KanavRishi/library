<?php
namespace App\Tests\Controller;

use App\Entity\Book;
use App\Entity\BookRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;
class BookControllerTest extends WebTestCase
{
    private $client;
    
    protected function setUp(): void
    {
        $this->client = static::createClient();
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();

        // Reset any custom exception handlers set during the test
        restore_exception_handler();
        restore_error_handler();
    }
    public function testAddNewBook()
    {
        $this->client->request(
                'POST',
                '/api/books/addBook',
                [],
                [],
                ['CONTENT_TYPE' => 'application/json'],
                json_encode([
                    'title' => 'Test Book',
                    'author' => 'Test Author',
                    'genre' => 'Fiction',
                    'isbn' => '1234567890',
                    'publishedDate' => '2024-07-25',
                    'status'=>'Available'
                ])
            );
            $responseContent = $this->client->getResponse()->getContent();
            $responseData = json_decode($responseContent, true);
            $lastInsertedId = $responseData['id'];
           $this->assertEquals(Response::HTTP_CREATED,$this->client->getResponse()->getStatusCode());
           $this->assertJsonStringEqualsJsonString(
            json_encode(['status'=>'Book added!','id'=>$lastInsertedId]),
            $this->client->getResponse()->getContent()
           );
        
        $this->assertEquals(201, $this->client->getResponse()->getStatusCode());
    }

    public function testViewListOfBooks()
    {
        $this->client->request('GET', '/api/books/');
        
        // Assert that the response status code is 200 OK
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        // Assert that the response content is a JSON array
        $this->assertJson($this->client->getResponse()->getContent());
    }

    public function testViewBookDetails()
    {
        $this->client->request('GET', '/api/books/12');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testEditBook()
    {
        $this->client->request('PUT', '/api/books/updateBook/10', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'title' => 'Updated Book',
        ]));

        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
    }

    public function testRemoveBook()
    {
        $this->client->request('DELETE', '/api/books/deleteBook/10');
        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
    }
}
