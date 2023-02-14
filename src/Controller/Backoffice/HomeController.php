<?php

namespace App\Controller\Backoffice;

use App\Repository\EventRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/", name="backoffice_")
 */
class HomeController extends AbstractController
{
    /**
     * @Route("", name="home")
     */
    public function index(EventRepository $eventRepository, UserRepository $userRepository, Request $request): Response
    {
        // EVENTS STATS
        $newlyCreatedEvents = count($eventRepository->findCreatedThisWeek());
        $events = count($eventRepository->findHappensThisWeek());

        // USERS STATS
        $newlyCreatedUsers = count($userRepository->findCreatedThisWeek());

        $interval = $request->query->get('interval');

        if ($interval === 'daily') {
            // EVENTS STATS
            $newlyCreatedEvents = count($eventRepository->findCreatedToday());
            $events = count($eventRepository->findHappensToday());

            // USERS STATS
            $newlyCreatedUsers = count($userRepository->findCreatedToday());
        } else if ($interval === 'monthly') {
            // EVENTS STATS
            $newlyCreatedEvents = count($eventRepository->findCreatedThisMonth());
            $events = count($eventRepository->findHappensThisMonth());

            // USERS STATS
            $newlyCreatedUsers = count($userRepository->findCreatedThisMonth());
        }

        // TODO: afficher le nombre d'évènements rejoints par un utilisateur
        // TODO: moyenne d'évènements créés et d'évènements rejoints
        // TODO: savoir combien d'organisateurs rejoignent un évènement

        return $this->render('backoffice/home/index.html.twig', [
            'interval' => $interval,
            // EVENTS STATS
            'newlyCreatedEvents' => $newlyCreatedEvents,
            'events' => $events,

            // USERS STATS
            'newlyCreatedUsers' => $newlyCreatedUsers,
        ]);
    }

    /**
     * @Route("/toggle-theme", name="toggle_theme")
     */
    public function toggleTheme(RequestStack $requestStack): Response
    {
        $session = $requestStack->getSession();

        if ($session->get('theme') !== null) {

            if ($session->get('theme') === 'darkly') {

                $session->set('theme', 'flatly');

            } else {

                $session->set('theme', 'darkly');

            }

        } else {

            $session->set('theme', 'flatly');

        }

        $refererRoute = $requestStack->getCurrentRequest()->headers->get('referer');
        return $this->redirect($refererRoute);
    }
}
