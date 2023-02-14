<?php

namespace App\Controller\Api\V1;

use App\Entity\Event;
use App\Entity\Favorite;
use App\Repository\FavoriteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * All methods related to a connected user favorites events
 * @Route("/api/v1/users/favorites", name="api_v1_users_favorites_", requirements={"id"="\d+"})
 */
class FavoriteController extends AbstractController
{
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @Route("", name="browse", methods={"GET"})
     */
    public function browse()
    {
        return $this->json($this->getUser()->getFavoriteEvents(), 200, [], [
            'groups' => ['favorite_browse'],
        ]);
    }

    /**
     * @Route("/{id}", name="add", methods={"POST"})
     */
    public function add(Event $event): Response
    {
        $user = $this->getUser();

        $favorite = new Favorite();
        $favorite->addEvent($event);
        $favorite->addUser($user);

        $this->manager->persist($favorite);

        $user->addFavoriteEvent($favorite);

        $this->manager->flush();

        return $this->json($favorite, 201, [], [
            'groups' => ['favorite_read'],
        ]);
    }

    /**
     *@Route("/{id}", name="delete", methods={"DELETE"})
     */
    public function delete(Favorite $favorite)
    {
        $this->denyAccessUnlessGranted('FAVORITE_DELETE', $favorite);

        $this->manager->remove($favorite);
        $this->manager->flush();

        return $this->json(null, 204);
    }
}
