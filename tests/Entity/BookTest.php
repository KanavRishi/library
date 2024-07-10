<?php
// tests/Entity/BookTest.php
namespace App\Tests\Entity;

use App\Entity\Book;
use PHPUnit\Framework\TestCase;

class BookTest extends TestCase
{
    // Test case to create a book and validate its properties
    public function testCreateBook()
    {
        // Create a new Book object
        $book = new Book();
        
        // Set properties of the book object
        $book->setTitle('Sample Book');
        $book->setAuthor('Author Name');
        $book->setGenre('Fiction');
        $book->setIsbn('1234567890');
        $book->setPublishedDate(new \DateTime());
        $book->setStatus('Available');

        // Assert that the getters return the expected values
        $this->assertEquals('Sample Book', $book->getTitle());
        $this->assertEquals('Author Name', $book->getAuthor());
        $this->assertEquals('Fiction', $book->getGenre());
        $this->assertEquals('1234567890', $book->getIsbn());
        $this->assertInstanceOf(\DateTime::class, $book->getPublishedDate());
        $this->assertEquals('Available', $book->getStatus());
    }
}
?>
