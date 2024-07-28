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
        $this->client->request('POST', '/api/users/addUser', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'username' => 'testuser',
            'email' => 'testuser@example.com',
            'password' => 'password123',
            'roles' => ['Member']
        ]));

        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
    }

    public function testViewListOfUsers()
    {
        $this->client->request('GET', '/api/users/');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testViewUserDetails()
    {
        $this->client->request('GET', '/api/users/1');
        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
    }

    public function testEditUser()
    {
        $this->client->request('PUT', '/api/users/updateUser/1', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'username' => 'updateduser',
        ]));

        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
    }

    public function testRemoveUser()
    {
        $this->client->request('DELETE', '/api/users/deleteUser/1');
        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
    }
}

?>
