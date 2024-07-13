<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

#[Route('/product')]
class ProductController extends AbstractController
{
    #[Route('/', name: 'app_products')]
    public function index(): Response
    {
        return $this->render('product/index.html.twig', [
            'controller_name' => 'ProductController',
        ]);
    }

    #[Route('/{id}', name: 'app_product', methods: ['GET'])]
    public function getProduct(ProductRepository $productRepository, int $id): Response
    {
        $product = $productRepository->findOneBy(['id' => $id]);

        return $this->render('product/product.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/new', name: 'app_product_new', methods: ['POST'])]
    public function newProduct(EntityManagerInterface $entityManager, Request $request): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($product);
            $entityManager->flush();
        }

        return $this->render('product/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
