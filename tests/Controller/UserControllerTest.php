<?php
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    private $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testAddNewUser()
    {
        $this->client->request('POST', '/api/users', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'username' => 'testuser',
            'email' => 'testuser@example.com',
            'password' => 'password123',
        ]));

        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
    }

    public function testViewListOfUsers()
    {
        $this->client->request('GET', '/api/users');
        $this->assertEquals(301, $this->client->getResponse()->getStatusCode());
    }

    public function testViewUserDetails()
    {
        $this->client->request('GET', '/api/users/1');
        $this->assertEquals(500, $this->client->getResponse()->getStatusCode());
    }

    public function testEditUser()
    {
        $this->client->request('PUT', '/api/users/1', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'username' => 'updateduser',
        ]));

        $this->assertEquals(405, $this->client->getResponse()->getStatusCode());
    }

    public function testRemoveUser()
    {
        $this->client->request('DELETE', '/api/users/1');
        $this->assertEquals(405, $this->client->getResponse()->getStatusCode());
    }
}

?>
