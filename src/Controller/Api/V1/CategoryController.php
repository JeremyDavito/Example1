<?php

namespace App\Controller\Api\V1;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/v1/categories", name="api_v1_categories_", requirements={"id"="\d+"})
 */
class CategoryController extends AbstractController
{
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @Route("", name="browse", methods={"GET"})
     */
    public function browse(Request $request, CategoryRepository $categoryRepository): Response
    {
        
        $limit = $request->query->get('limit');

        if ($limit) {
            return $this->json(
                $categoryRepository->findBy(
                    [],
                    null,
                    $limit
                ),
                200,
                [],
                [
                    'groups' => ['category_browse']
                ]
            );
        }

        return $this->json($categoryRepository->findAll(), 200, [], [
            'groups' => ['category_browse'],
        ]);
    }

    /**
     * @Route("", name="add", methods={"POST"})
     */
    public function add(Request $request): Response
    {
        $category = new Category();

        $form = $this->createForm(CategoryType::class, $category, ['csrf_protection' => false]);

        $json = $request->getContent();
        $jsonArray = json_decode($json, true);

        $form->submit($jsonArray);

        if ($form->isValid()) {
            $this->manager->persist($category);
            $this->manager->flush();

            return $this->json($category, 201, [], [
                'groups' => ['category_read']
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
    public function edit(Category $category, Request $request): Response
    {
        $form = $this->createForm(CategoryType::class, $category, ['csrf_protection' => false]);

        $json = $request->getContent();
        $jsonArray = json_decode($json, true);

        $form->submit($jsonArray);

        if ($form->isValid()) {
            $this->manager->flush();

            return $this->json($category, 200, [], [
                'groups' => ['category_read']
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
    public function delete(Category $category)
    {
        $this->manager->remove($category);
        $this->manager->flush();

        return $this->json(null, 204);
    }
}
