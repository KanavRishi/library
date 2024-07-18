<?php
// tests/Entity/UserTest.php
namespace App\Tests\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class BorrowTest extends TestCase
{
    // Test case to create a user and validate its properties
    public function testCreateUser()
    {
        // Create a new User object
        $user = new User();
        
        // Set properties of the user object
        $user->setName('John Doe');
        $user->setEmail('john.doe@example.com');
        $user->setRole('Member');
        $user->setPassword('password123');

        // Assert that the getters return the expected values
        $this->assertEquals('John Doe', $user->getName());
        $this->assertEquals('john.doe@example.com', $user->getEmail());
        $this->assertEquals('Member', $user->getRole());
        $this->assertEquals('password123', $user->getPassword());
    }
}
?>
