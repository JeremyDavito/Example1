<?php

namespace App\Controller\Api\V1;

use App\Entity\User;
use App\Entity\Event;
use App\Form\EventOnlineType;
use App\Form\EventType;
use App\Repository\EventRepository;
use App\Repository\UserRepository;
use App\Service\ApiImageUploader;
use App\Service\FileUploader;
use App\Service\UploadedBase64File;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/api/v1/events", name="api_v1_events_", requirements={"id"="\d+"})
 */
class EventController extends AbstractController
{
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @Route("", name="browse", methods={"GET"})
     */
    public function browse(Request $request, EventRepository $eventRepository): Response
    {
       
        $limit = $request->query->get('limit');
        $categoryId = $request->query->get('category');

        if ($categoryId) {
            return $this->json(
                $eventRepository->findByCategory($categoryId, $limit),
                200,
                [],
                [
                    'groups' => ['event_browse']
                ]
            );
        }

        $keyword = $request->query->get('search');

        if (isset($keyword)) {
            return $this->json(
                $eventRepository->findByKeyword($keyword, $limit),
                200,
                [],
                [
                    'groups' => ['event_browse']
                ]
            );
        }

        return $this->json(
            $eventRepository->findByActive($limit),
            200,
            [],
            [
                'groups' => ['event_browse']
            ]
        );
    }

    /**
     * @Route("/past", name="browse_past", methods={"GET"})
     */
    public function browseUserPastEvents(Request $request, EventRepository $eventRepository): Response
    {
        $limit = $request->query->get('limit');

        // this is how we get the connected user
        $user = $this->getUser();
        $userId = $user->getId();

        $userPastEvents = $eventRepository->findPastEvents($userId, $limit);

        return $this->json($userPastEvents, 200, [], [
            'groups' => ['event_browse'],
        ]);
    }

    /**
     * @Route("/incoming", name="browse_incoming", methods={"GET"})
     */
    public function browseUserIncomingEvents(Request $request, EventRepository $eventRepository): Response
    {
        $limit = $request->query->get('limit');

        // this is how we get the connected user
        $user = $this->getUser();
        $userId = $user->getId();

        $userIncomingEvents = $eventRepository->findIncomingEvents($userId, $limit);

        return $this->json($userIncomingEvents, 200, [], [
            'groups' => ['event_browse'],
        ]);
    }

    /**
     * @Route("/{id}", name="read", methods={"GET"})
     */
    public function read(Event $event, EventRepository $eventRepository): Response
    {
        $recommendedEvents = $eventRepository->findRecommendedEvents($event);

        return $this->json([
            'event' => $event,
            'recommendedEvents' => $recommendedEvents,
        ], 200, [], [
            'groups' => ['event_read'],
        ]);
    }

    /**
     * @Route("", name="add", methods={"POST"})
     */
    public function add(ApiImageUploader $apiImageUploader, EventRepository $eventRepository, Request $request): Response
    {
        $event = new Event();

        $online = $request->query->get('type');

        if (isset($online)) {
            $event->setIsOnline(true);
            // we pre-filled the unnecessary fields for an online event
            $event->setZipcode('');
            $event->setAddress('');
            $event->setCity('');
            $event->setCountry('');
            $form = $this->createForm(EventOnlineType::class, $event, ['csrf_protection' => false]);
        } else {
            $event->setIsOnline(false);
            $form = $this->createForm(EventType::class, $event, ['csrf_protection' => false]);
        }

        $json = $request->getContent();

        $jsonArray = json_decode($json, true);

        if (array_key_exists('picture', $jsonArray)) {
            // Get picture infos
            $imageName = $jsonArray['picture']['name'];
            $imageBase64Value = $jsonArray['picture']['value'];

            $newImageName = $apiImageUploader->uploadBase64Image($imageName, $imageBase64Value, '/events');

            $event->setPicture($newImageName);

            // After the image uploads, we remove the picture field in the json datas
            unset($jsonArray['picture']);
        } else {
            $event->setPicture('event_placeholder.jpg');
        }

        $form->submit($jsonArray, false);

        if ($form->isValid()) {
            $event->setAuthor($this->getUser());
            // add the event author as a member
            $event->addMember($event->getAuthor());

            $this->manager->persist($event);
            $this->manager->flush();

            $recommendedEvents = $eventRepository->findRecommendedEvents($event);

            return $this->json([
                'event' => $event,
                'recommendedEvents' => $recommendedEvents,
            ], 201, [], [
                'groups' => ['event_read'],
            ]);
        }

        $errorMessages = [];
        foreach ($form->getErrors(true) as $error) {
            $errorMessages[] = [
                'message' => $error->getMessage(),
                'property' => $error->getOrigin()->getName(),
            ];
        }

        return $this->json($errorMessages, 400);
    }

    /**
     * @Route("/{id}", name="edit", methods={"PUT", "PATCH"})
     */
    public function edit(ApiImageUploader $apiImageUploader, Event $event, EventRepository $eventRepository, Request $request): Response
    {
        // control if the event author is the user who want to edit the event
        $this->denyAccessUnlessGranted('EVENT_EDIT', $event);

        

        if ($event->getIsOnline() === true) {
            $form = $this->createForm(EventOnlineType::class, $event, ['csrf_protection' => false]);
        } else {
            $form = $this->createForm(EventType::class, $event, ['csrf_protection' => false]);
        }

        $json = $request->getContent();
        $jsonArray = json_decode($json, true);

        if (array_key_exists('picture', $jsonArray)) {

            // Get picture infos
            $imageName = $jsonArray['picture']['name'];
            $imageBase64Value = $jsonArray['picture']['value'];

            $newImageName = $apiImageUploader->uploadBase64Image($imageName, $imageBase64Value, '/events');

            $event->setPicture($newImageName);

            // After the image uploads, we remove the picture field in the json datas
            unset($jsonArray['picture']);
        }

        $form->submit($jsonArray, false);

        if ($form->isValid()) {
            $event->setUpdatedAt(new \DateTimeImmutable());
            $this->manager->flush();

            $recommendedEvents = $eventRepository->findRecommendedEvents($event);

            return $this->json([
                'event' => $event,
                'recommendedEvents' => $recommendedEvents,
            ], 200, [], [
                'groups' => ['event_read']
            ]);
        }

        $errorMessages = [];
        foreach ($form->getErrors(true) as $error) {
            $errorMessages[] = [
                'message' => $error->getMessage(),
                'property' => $error->getOrigin()->getName(),
            ];
        }

        return $this->json($errorMessages, 400);
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     */
    public function delete(Event $event): Response
    {
        // control if the event author is the user who want to delete the event
        $this->denyAccessUnlessGranted('EVENT_DELETE', $event);

        $this->manager->remove($event);
        $this->manager->flush();

        return $this->json(null, 204);
    }

    /**
     * @Route("/{id}/add", name="add_member", methods={"POST"})
     */
    public function addMember(Event $event)
    {
        // control if event max members limit is not already reached
        $this->denyAccessUnlessGranted('EVENT_ADD_MEMBER', $event);

        foreach ($event->getMembers() as $member) {
            if ($this->getUser()->getId() === $member->getId()) {
                return $this->json([
                    'message' => 'Already a member.',
                ], 403);
            }
        }

        $event->addMember($this->getUser());
        $this->manager->flush();

        return $this->json($event, 200, [], [
            'groups' => 'event_read'
        ]);
    }

    /**
     * @Route("/{id}/remove", name="remove_member", methods={"DELETE"})
     */
    public function removeMember(Event $event)
    {
        if ($this->getUser() === $event->getAuthor()) {
            return $this->json([
                'message' => 'U cannot remove the author as a member',
            ], 403);
        }

        $event->removeMember($this->getUser());
        $this->manager->flush();

        return $this->json($event, 200, [], [
            'groups' => 'event_read'
        ]);
    }
}
