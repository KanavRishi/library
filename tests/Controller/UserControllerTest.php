<?php
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    public function testAddNewUser()
    {
        $client = static::createClient();
        $client->request('POST', '/api/users', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'role' => 'Member',
            'password' => 'securepassword'
        ]));

        $this->assertEquals(201, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());
    }

    public function testViewListOfUsers()
    {
        $client = static::createClient();
        $client->request('GET', '/api/users');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());
    }

    public function testViewUserDetails()
    {
        $client = static::createClient();
        $client->request('GET', '/api/users/1');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());
    }

    public function testEditUser()
    {
        $client = static::createClient();
        $client->request('PUT', '/api/users/1', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'name' => 'Updated Test User',
            'email' => 'updatedtestuser@example.com',
            'role' => 'Admin',
            'password' => 'newsecurepassword'
        ]));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());
    }

    public function testRemoveUser()
    {
        $client = static::createClient();
        $client->request('DELETE', '/api/users/1');

        $this->assertEquals(204, $client->getResponse()->getStatusCode());
    }
}
?>