<?php

namespace App\Tests\Api;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserTest extends WebTestCase
{
    public function testWhileNonAuthenticated(): void
    {
        $client = static::createClient();
        $client->insulate(true);
        $crawler = $client->jsonRequest('POST', '/api/v1/users', [
            'email' => MainTest::generateRandomString() . '@gmail.com',
            'password' => 'Testing123!',
            'firstname' => 'test',
            'lastname' => 'oclock',
        ]);
        $data = json_decode($client->getResponse()->getContent(), true);
        $newUserId = $data['id'];
        $this->assertResponseStatusCodeSame(201);

        // GET
        $client->request('GET', '/api/v1/users/' . $newUserId);
        $this->assertResponseStatusCodeSame(401);

        // PUT / PATCH
        $client->request('PUT', '/api/v1/users');
        $this->assertResponseStatusCodeSame(401);

        $client->request('PATCH', '/api/v1/users');
        $this->assertResponseStatusCodeSame(401);

        // DELETE
        $client->request('DELETE', '/api/v1/users/' . $newUserId);
        $this->assertResponseStatusCodeSame(401);

        // once I've been executed this tests above I delete the new user created
        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->find($newUserId);

        $kernel = self::bootKernel();
        $manager = $kernel->getContainer()->get('doctrine')->getManager();
        // we have to merge the user so Doctrine don't throw an error for removing the user because it is a detached entity
        $user = $manager->merge($user);
        $manager->remove($user);
        $manager->flush();

        // avoid memory leaks like this
        parent::tearDown();
        $manager->close();
        $manager = null;
    }

    public function testWhileAuthenticated(): void
    {
        $client = static::createClient();
        $client->insulate();

        $userEmailString = MainTest::generateRandomString();

        // POST
        $crawler = $client->jsonRequest('POST', '/api/v1/users', [
            'email' => $userEmailString . '@gmail.com',
            'password' => 'Testing123!',
            'firstname' => 'test',
            'lastname' => 'oclock',
        ]);
        $data = json_decode($client->getResponse()->getContent(), true);
        $newUserId = $data['id'];
        $this->assertResponseStatusCodeSame(201);

        $client = MainTest::createAuthenticatedClient($userEmailString . '@gmail.com', 'Testing123!');

        // GET
        $client->request('GET', '/api/v1/users/' . $newUserId);
        $this->assertResponseStatusCodeSame(200);

        // PUT / PATCH
        $client->jsonRequest('PUT', '/api/v1/users', [
            'address' => '16 rue de l\'Eglise',
        ]);
        $this->assertResponseStatusCodeSame(200);

        $client->jsonRequest('PATCH', '/api/v1/users', [
            'zipcode' => '95200',
        ]);
        $this->assertResponseStatusCodeSame(200);

        // DELETE
        $client->request('DELETE', '/api/v1/users/' . $newUserId);
        $this->assertResponseStatusCodeSame(204);
    }
}
