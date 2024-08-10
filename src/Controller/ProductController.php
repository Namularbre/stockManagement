<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use App\Service\ImageService;
use Doctrine\ORM\EntityManagerInterface;
use League\Flysystem\FilesystemException;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DomCrawler\Image;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

#[Route('/products')]
class ProductController extends AbstractController
{
    #[Route('/', name: 'app_products', methods: ['GET'])]
    public function index(ProductRepository $productRepository, Request $request): Response
    {
        $page = $request->query->getInt('page', 1);

        $paginator = $productRepository->findByPage($page);

        return $this->render('product/index.html.twig', [
            'paginator' => $paginator,
            'currentPage' => $page,
            'totalPages' => ceil($paginator->count() / ProductRepository::LIMIT),
        ]);
    }

    #[Route('/product/{id}', name: 'app_product', methods: ['GET'])]
    public function getProduct(ProductRepository $productRepository, int $id, ImageService $imageService, LoggerInterface $logger): Response
    {
        $product = $productRepository->findOneBy(['id' => $id]);

        if (isset($product)) {
            $imageName = $product->getImageName();
            $imageFile = null;

            if (isset($imageName)) {
                try {
                    $imageFile = $imageService->getObject($imageName);
                } catch (FilesystemException $exception) {
                    $logger->error('Error recovering product image: ' . $exception->getMessage());
                    return new Response('Internal server error', Response::HTTP_INTERNAL_SERVER_ERROR);
                }
            }

            return $this->render('product/product.html.twig', [
                'product' => $product,
                'imageFile' => $imageFile,
            ]);
        }

        throw $this->createNotFoundException('Product not found');
    }

    #[Route('/new', name: 'app_product_new', methods: ['GET', 'POST'])]
    public function newProduct(EntityManagerInterface $entityManager, Request $request, ImageService $imageService, LoggerInterface $logger): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
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

                $this->addFlash('success', 'Product added successfully');
            } else {
                $error = $form->getErrors(true)[0]->getMessage();
                $this->addFlash('danger', $error);
            }
            return $this->redirectToRoute('app_product_new');
        }

        return $this->render('product/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    private function cleanAlerts(EntityManagerInterface $entityManager, Product $product): void
    {
        foreach ($product->getAlerts() as $alert) {
            $alert->removeProduct($product);

            if ($alert->getProducts()->isEmpty()) {
                $entityManager->remove($alert);
            }
        }
    }

    #[Route('/delete/{id}', name: 'app_product_delete', methods: ['DELETE'])]
    public function deleteProduct(EntityManagerInterface $entityManager, int $id): Response
    {
        $product = $entityManager->getRepository(Product::class)->findOneBy(['id' => $id]);

        if (isset($product)) {
            $this->cleanAlerts($entityManager, $product);

            $entityManager->remove($product);
            $entityManager->flush();

            return new Response('Product removed successfully', Response::HTTP_OK);
        }

        throw $this->createNotFoundException('Product not found.');
    }

    #[Route('/update/{id}', name: 'app_product_update', methods: ['GET', 'PUT'])]
    public function updateProduct(EntityManagerInterface $entityManager, int $id, Request $request, ImageService $imageService, LoggerInterface $logger): Response
    {
        $product = $entityManager->getRepository(Product::class)->findOneBy(['id' => $id]);

        if (!isset($product)) {
            throw $this->createNotFoundException('Product not found');
        }

        $form = $this->createForm(ProductType::class, $product, [
            'method' => Request::METHOD_PUT,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
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

                $this->addFlash('success', 'Product updated');
            } else {
                $error = $form->getErrors(true)[0]->getMessage();
                $this->addFlash('danger', $error);
            }
            return $this->redirectToRoute('app_product_update', [
                'id' => $product->getId(),
            ]);
        }

        return $this->render('product/update.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/reduce/{id}', name: 'app_product_reduce', methods: ['PUT'])]
    public function reduceQuantity(EntityManagerInterface $entityManager, int $id): Response
    {
        $product = $entityManager->getRepository(Product::class)->findOneBy(['id' => $id]);

        if (isset($product)) {
            $qty = $product->getQuantity();

            if ($qty > 0) {
                $qty -= 1;
                $product->setQuantity($qty);

                $entityManager->persist($product);
                $entityManager->flush();

                return new Response('Product updated.', Response::HTTP_OK);
            } else {
                return new Response('Quantity cannot be negative', Response::HTTP_OK);
            }
        }

        throw $this->createNotFoundException('Product not found');
    }

    #[Route('/add/{id}', name: 'app_product_add', methods: ['PUT'])]
    public function addQuantity(EntityManagerInterface $entityManager, int $id): Response
    {
        $product = $entityManager->getRepository(Product::class)->findOneBy(['id' => $id]);

        if (isset($product)) {
            $qty = $product->getQuantity();
            $qty += 1;
            $product->setQuantity($qty);

            $entityManager->persist($product);
            $entityManager->flush();

            return new Response('Product updated.', Response::HTTP_OK);
        }

        throw $this->createNotFoundException('Product not found');
    }
}
