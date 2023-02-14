<?php

namespace App\Controller\Api\V1;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Service\ApiImageUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * @Route("/api/v1/users", name="api_v1_users_", requirements={"id"="\d+"})
 */
class UserController extends AbstractController
{
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @Route("", name="browse", methods={"GET"})
     */
    public function browse(UserRepository $userRepository): Response
    {
        return $this->json($userRepository->findAll(), 200, [], [
            'groups' => ['user_browse'],
        ]);
    }

    /**
     * @Route("/{id}", name="read", methods={"GET"})
     */
    public function read(User $user): Response
    {
        return $this->json($user, 200, [], [
            'groups' => ['user_read'],
        ]);
    }

    /**
     * @Route("", name="add", methods={"POST"})
     */
    public function add(ApiImageUploader $apiImageUploader, Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new User();

        $form = $this->createForm(UserType::class, $user, ['csrf_protection' => false]);

        $json = $request->getContent();
        $jsonArray = json_decode($json, true);

        if (array_key_exists('picture', $jsonArray)) {
            // Get picture infos
            $imageName = $jsonArray['picture']['name'];
            $imageBase64Value = $jsonArray['picture']['value'];

            $newImageName = $apiImageUploader->uploadBase64Image($imageName, $imageBase64Value, '/users');

            $user->setAvatar($newImageName);

            // After the image uploads, we remove the picture field in the json datas
            unset($jsonArray['picture']);
        } else {
            $user->setAvatar('user_placeholder.png');
        }

        $form->submit($jsonArray);

        if ($form->isValid()) {
            $plainTextPassword = $form->get('password')->getData();

            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $plainTextPassword
            );

            $user->setPassword($hashedPassword);

            $this->manager->persist($user);
            $this->manager->flush();

            return $this->json($user, 201, [], [
                'groups' => ['user_read']
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
     * @Route("", name="edit", methods={"PUT", "PATCH"})
     */
    public function edit(ApiImageUploader $apiImageUploader, UserRepository $userRepository, Request $request): Response
    {
        $user = $userRepository->find($this->getUser()->getId());
        // TODO: permettre de modifier son mot de passe 
        // control if the connected user is the user that is modified
        $this->denyAccessUnlessGranted('USER_EDIT', $user);

        $form = $this->createForm(UserType::class, $user, ['csrf_protection' => false]);

        $json = $request->getContent();
        $jsonArray = json_decode($json, true);

        if (array_key_exists('picture', $jsonArray)) {
            // Get picture infos
            $imageName = $jsonArray['picture']['name'];
            $imageBase64Value = $jsonArray['picture']['value'];

            $newImageName = $apiImageUploader->uploadBase64Image($imageName, $imageBase64Value, '/users');

            $user->setAvatar($newImageName);

            // After the image uploads, we remove the picture field in the json datas
            unset($jsonArray['picture']);
        }

        $form->submit($jsonArray, false);

        if ($form->isValid()) {
            $this->manager->flush();

            return $this->json($user, 200, [], [
                'groups' => ['user_read']
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
    public function delete(User $user): Response
    {
        // control if the connected user is the user that is deleted
        $this->denyAccessUnlessGranted('USER_DELETE', $user);

        $this->manager->remove($user);
        $this->manager->flush();

        return $this->json(null, 204);
    }
}
