<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\AdminProductType;
use App\Repository\ProductRepository;
use App\Service\ImageService;
use Doctrine\ORM\EntityManagerInterface;
use League\Flysystem\FilesystemException;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/product')]
class AdminProductController extends AbstractController
{
    #[Route('/', name: 'app_admin_product_index', methods: ['GET'])]
    public function index(ProductRepository $productRepository): Response
    {
        return $this->render('admin_product/index.html.twig', [
            'products' => $productRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_admin_product_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, ImageService $imageService, LoggerInterface $logger): Response
    {
        $product = new Product();
        $form = $this->createForm(AdminProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $product->getImageFile();

            if (isset($image)) {
                try {
                    $imageName = $imageService->putObject($product->getName(), $image->getContent());
                    $product->setImageName($imageName);
                } catch (FilesystemException $exception) {
                    $logger->error('Unable to upload file into min.io: ' . $exception->getMessage());
                    return new Response('Internal server error', Response::HTTP_INTERNAL_SERVER_ERROR);
                }
            }

            $entityManager->persist($product);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin_product/new.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_product_show', methods: ['GET'])]
    public function show(Product $product): Response
    {
        return $this->render('admin_product/show.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_product_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Product $product, EntityManagerInterface $entityManager, ImageService $imageService, LoggerInterface $logger): Response
    {
        $form = $this->createForm(AdminProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $product->getImageFile();

            if (isset($image)) {
                try {
                    $imageName = $imageService->putObject($product->getName(), $image->getContent());
                    $product->setImageName($imageName);
                } catch (FilesystemException $exception) {
                    $logger->error('Unable to upload file into min.io: ' . $exception->getMessage());
                    return new Response('Internal server error', Response::HTTP_INTERNAL_SERVER_ERROR);
                }
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_admin_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin_product/edit.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_product_delete', methods: ['POST'])]
    public function delete(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($product);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_product_index', [], Response::HTTP_SEE_OTHER);
    }
}
