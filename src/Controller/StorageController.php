<?php

namespace App\Controller;

use App\Entity\Storage;
use App\Form\StorageType;
use App\Repository\StorageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/storages')]
class StorageController extends AbstractController
{
    #[Route('/', name: 'app_storages', methods: ['GET'])]
    public function index(StorageRepository $storageRepository): Response
    {
        $storages = $storageRepository->findAll();

        return $this->render('storage/index.html.twig', [
            'controller_name' => 'StorageController',
            'storages' => $storages
        ]);
    }

    #[Route('/new', name: 'app_storage_new', methods: ['GET', 'POST'])]
    public function newStorage(EntityManagerInterface $entityManager, Request $request): Response
    {
        $storage = new Storage();
        $form = $this->createForm(StorageType::class, $storage);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $entityManager->persist($storage);
                $entityManager->flush();

                $this->addFlash('success', 'Storage added successfully');
            } else {
                $error = $form->getErrors(true)[0]->getMessage();
                $this->addFlash('danger', $error);
            }
            return $this->redirectToRoute('app_storage_new');
        }

        return $this->render('storage/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/products/{id}', name: 'app_storage_products', methods: ['GET'])]
    public function getProducts(StorageRepository $storageRepository, int $id): Response
    {
        $storage = $storageRepository->findOneBy(['id' => $id]);

        if (isset($storage)) {
            $products = $storage->getProducts();

            return $this->render('storage/products.html.twig', [
                'storage' => $storage,
                'products' => $products
            ]);
        }

        throw $this->createNotFoundException('Storage not found');
    }
}
