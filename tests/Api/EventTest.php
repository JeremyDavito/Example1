<?php

namespace App\Tests\Api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class EventTest extends WebTestCase
{
    /**
     * @dataProvider publicGetUrlList
     */
    public function testWhileNonAuthenticated($publicGetUrlList): void
    {
        $client = static::createClient();
        $client->insulate(true);
        $client->request('GET', $publicGetUrlList);
        $this->assertResponseIsSuccessful();

        // GET
        $client->request('GET', '/api/v1/events/past');
        $this->assertResponseStatusCodeSame(401);

        $client->request('GET', '/api/v1/events/incoming');
        $this->assertResponseStatusCodeSame(401);

        // POST
        $client->jsonRequest('POST', '/api/v1/events', [
            'title' => "match de volley",
            'description' => 'venez vous joindre à nous pour ce match de volley !',
            'date' => '2022-02-12 10:55:00',
            'category' => 1,
            'maxMembers' => 50,
            'picture' => '',
            'address' => '16 rue test',
            'zipcode' => '95200',
            'city' => 'Sarcelles',
            'country' => 'France'
        ]);
        $this->assertResponseStatusCodeSame(401);

        $client->jsonRequest('POST', '/api/v1/events?type=online', [
            'title' => 'match de volley',
            'description' => 'venez vous joindre à nous pour ce match de volley !',
            'date' => '2022-02-12 10:55:00',
            'category' => 1,
            'maxMembers' => 50,
            'picture' => '',
        ]);
        $this->assertResponseStatusCodeSame(401);

        $client->request('POST', '/api/v1/events/2/add');
        $this->assertResponseStatusCodeSame(401);

        // PUT / PATCH
        $client->jsonRequest('PUT', '/api/v1/events/2', [
            'title' => 'je veux faire un test',
        ]);
        $this->assertResponseStatusCodeSame(401);

        $client->jsonRequest('PATCH', '/api/v1/events/2', [
            'title' => 'je veux faire un autre test',
        ]);
        $this->assertResponseStatusCodeSame(401);

        // DELETE
        $client->request('DELETE', '/api/v1/events/2/remove');
        $this->assertResponseStatusCodeSame(401);

        $client->request('DELETE', '/api/v1/events/2');
        $this->assertResponseStatusCodeSame(401);

        $client->request('DELETE', '/api/v1/events/2');
        $this->assertResponseStatusCodeSame(401);
    }

    public function testOtherUrlsWhileAuthenticated() :void
    {
        $client = MainTest::createAuthenticatedClient('admin@gmail.com', 'test');
        $client->insulate(true);

        // GET
        $client->request('GET', '/api/v1/events/past');
        $this->assertResponseStatusCodeSame(200);

        $client->request('GET', '/api/v1/events/incoming');
        $this->assertResponseStatusCodeSame(200);

        // POST
        $client->jsonRequest('POST', '/api/v1/events', [
            'title' => "match de volley",
            'description' => 'venez vous joindre à nous pour ce match de volley !',
            'date' => '2022-02-12 10:55:00',
            'category' => 1,
            'maxMembers' => 50,
            'picture' => '',
            'address' => '16 rue test',
            'zipcode' => '95200',
            'city' => 'Sarcelles',
            'country' => 'France'
        ]);
        $data = json_decode($client->getResponse()->getContent(), true);
        $newEventId = $data['event']['id'];
        $this->assertResponseStatusCodeSame(201);

        $client->jsonRequest('POST', '/api/v1/events?type=online', [
            'title' => 'match de volley',
            'description' => 'venez vous joindre à nous pour ce match de volley !',
            'date' => '2022-02-12 10:55:00',
            'category' => 1,
            'maxMembers' => 50,
            'picture' => '',
        ]);
        $data = json_decode($client->getResponse()->getContent(), true);
        $newOnlineEventId = $data['event']['id'];
        $this->assertResponseStatusCodeSame(201);

        $client->request('POST', '/api/v1/events/' . $newEventId . '/add');
        $this->assertResponseStatusCodeSame(403);

        // PUT / PATCH
        $client->jsonRequest('PUT', '/api/v1/events/' . $newEventId, [
            'title' => 'je veux faire un test',
        ]);
        $this->assertResponseStatusCodeSame(200);

        $client->jsonRequest('PATCH', '/api/v1/events/' . $newEventId, [
            'title' => 'je veux faire un autre test',
        ]);
        $this->assertResponseStatusCodeSame(200);

        // DELETE
        $client->request('DELETE', '/api/v1/events/' . $newEventId . '/remove');
        $this->assertResponseStatusCodeSame(403);

        $client->request('DELETE', '/api/v1/events/' . $newEventId);
        $this->assertResponseStatusCodeSame(204);

        $client->request('DELETE', '/api/v1/events/' . $newOnlineEventId);
        $this->assertResponseStatusCodeSame(204);
    }

    /**
     * @dataProvider publicGetUrlList
     */
    public function testGetUrlsWhileAuthenticated($publicGetUrlList): void
    {
        $client = MainTest::createAuthenticatedClient('admin@gmail.com', 'test');
        $client->insulate(true);
        $client->request('GET', $publicGetUrlList);
        $this->assertResponseIsSuccessful();
    }

    public function publicGetUrlList(): array
    {
        return [
            ['/api/v1/events'],
            ['/api/v1/events?limit=2'],
            ['/api/v1/events?category=1'],
            ['/api/v1/events/2'],
            ['/api/v1/events?search=volley'],
        ];
    }
}
