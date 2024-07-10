<?php
// tests/Entity/BookTest.php
namespace App\Tests\Entity;

use App\Entity\Book;
use PHPUnit\Framework\TestCase;

class BookTest extends TestCase
{
    public function testCreateBook()
    {
        $book = new Book();
        $book->setTitle('Sample Book');
        $book->setAuthor('Author Name');
        $book->setGenre('Fiction');
        $book->setIsbn('1234567890');
        $book->setPublishedDate(new \DateTime());
        $book->setStatus('Available');

        $this->assertEquals('Sample Book', $book->getTitle());
        $this->assertEquals('Author Name', $book->getAuthor());
        $this->assertEquals('Fiction', $book->getGenre());
        $this->assertEquals('1234567890', $book->getIsbn());
        $this->assertInstanceOf(\DateTime::class, $book->getPublishedDate());
        $this->assertEquals('Available', $book->getStatus());
    }
}
?>