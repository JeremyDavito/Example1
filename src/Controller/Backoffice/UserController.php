<?php

namespace App\Controller\Backoffice;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/users", name="backoffice_users_", requirements={"id"="\d+"})
 */
class UserController extends AbstractController
{
    /**
     * @Route("", name="browse")
     */
    public function browse(UserRepository $userRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $users = $paginator->paginate(
            $userRepository->findAll(),
            $request->query->getInt('page', 1),
            8
        );

        return $this->render('backoffice/users/browse.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * @Route("/{id}", name="read")
     */
    public function read(User $user): Response
    {
        return $this->render('backoffice/users/read.html.twig', [
            'user' => $user,
        ]);
    }

    // TODO: permettre l'édition d'un compte quand il appartient a l'utilisateur connecté au backoffice

    /**
     * @Route("/{id}/toggle-role-moderator", name="toggle_role_moderator")
     */
    public function toggleRoleModerator(EntityManagerInterface $manager, User $user)
    {
        // we passed the connected user as subject just because the voter verify if the subject is an instace of App\Entity\User
        $this->denyAccessUnlessGranted('USER_TOGGLE_ROLE', $this->getUser());

        if (in_array('ROLE_MODERATOR', $user->getRoles())) {

            $user->setRoles([]);

        } else {

            $user->setRoles(['ROLE_MODERATOR']);

        }

        $manager->flush();

        return $this->redirectToRoute('backoffice_users_browse');

    }

    /**
     * @Route("/{id}/delete", name="delete")
     */
    public function delete(EntityManagerInterface $manager, User $user): Response
    {
        // TODO: ajouter protection csrf
        $manager->remove($user);
        $manager->flush();

        $this->addFlash('message', 'L\'utilisateur a bien été supprimé');

        return $this->redirectToRoute('backoffice_users_browse');
    }

    /**
     * @Route("/{id}/toggle-activate", name="toggle_activate")
     */
    public function toggleActivate(User $user, EntityManagerInterface $manager): Response
    {
        $user->setIsActivate(!$user->getIsActivate());
        $manager->flush();

        return $this->redirectToRoute('backoffice_users_browse');
    }
}
