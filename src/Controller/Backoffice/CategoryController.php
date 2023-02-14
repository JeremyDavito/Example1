<?php

namespace App\Controller\Backoffice;

use App\Entity\Category;
use App\Form\Back\CategoryType;
use App\Repository\CategoryRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/categories", name="backoffice_categories_", requirements={"id"="\d+"})
 */
class CategoryController extends AbstractController
{
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @Route("", name="browse")
     */
    public function browse(CategoryRepository $categoryRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $categories = $paginator->paginate(
            $categoryRepository->findAll(),
            $request->query->getInt('page', 1),
            8
        );

        return $this->render('backoffice/categories/browse.html.twig', [
            'categories' => $categories,
        ]);
    }

    /**
     * @Route("/{id}", name="read")
     */
    public function read(Category $category): Response
    {
        return $this->render('backoffice/categories/read.html.twig', [
            'category' => $category,
        ]);
    }

    /**
     * @Route("/add", name="add")
     */
    public function add(Request $request): Response
    {
        $category = new Category();

        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($category->getPicture() === null) {
                $category->setPicture('category_placeholder.jpg');
            }

            $this->manager->persist($category);
            $this->manager->flush();

            $this->addFlash('success', 'La catégorie a bien été ajoutée.');

            return $this->redirectToRoute('backoffice_categories_read', ['id' => $category->getId()]);
        }

        return $this->render('backoffice/categories/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="edit")
     */
    public function edit(Request $request, Category $category): Response
    {
        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->manager->flush();

            $this->addFlash('success', 'La catégorie a bien été modifiée.');

            return $this->redirectToRoute('backoffice_categories_read', ['id' => $category->getId()]);
        }


        return $this->render('backoffice/categories/edit.html.twig', [
            'category' => $category,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/delete", name="delete", methods={"DELETE"})
     */
    public function delete(Category $category, Request $request): Response
    {
        $token = $request->request->get('_token');

        if ($this->isCsrfTokenValid('delete_categories_' . $category->getId(), $token)) {

            $this->manager->remove($category);
            $this->manager->flush();

            $this->addFlash('success', 'La catégorie a bien été supprimée.');

            return $this->redirectToRoute('backoffice_categories_browse');
        }

        $this->addFlash('danger', 'La catégorie n\'a pas bien été supprimée.');

        return $this->redirectToRoute('backoffice_categories_browse');
    }
}
