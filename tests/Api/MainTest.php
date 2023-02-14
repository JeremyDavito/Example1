<?php

namespace App\Tests\Api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MainTest extends WebTestCase
{
    public function testAPILogin()
    {
        $client = self::createClient();
        $client->jsonRequest('GET', '/api/login_check', [
            'username' => 'admin@gmail.com',
            'password' => 'test',
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertJson($client->getResponse()->getContent());
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('token', $data);

        $client->jsonRequest('GET', '/api/login_check', [
            'username' => 'dummy@gmail.com',
            'password' => 'dummy',
        ]);

        $this->assertResponseStatusCodeSame(401);
        $this->assertJson($client->getResponse()->getContent());
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayNotHasKey('token', $data);
    }

    public static function generateRandomString(int $length = 10): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $maximumLength = strlen($characters) - 1;
        $randomString = '';
        for ($index = 1; $index <= $length; $index++) {
            $randomString .= $characters[rand(0, $maximumLength)];
        }

        return $randomString;
    }

    /**
     * Create a client with a default Authorization header.
     */
    public static function createAuthenticatedClient(string $username, string $password)
    {
        self::ensureKernelShutdown();
        $client = static::createClient();
        $client->jsonRequest(
            'POST',
            '/api/login_check',
            [
                'username' => $username,
                'password' => $password,
            ]
        );

        $data = json_decode($client->getResponse()->getContent(), true);

        $client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $data['token']));

        return $client;
    }
}
