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
        $ŝtorages = $storageRepository->findAll();

        return $this->render('storage/index.html.twig', [
            'controller_name' => 'StorageController',
            'storages' => $ŝtorages
        ]);
    }

    #[Route('{id}', name: 'app_storage', methods: ['GET'])]
    public function getStorage(StorageRepository $storageRepository, int $id): Response
    {
        $storage = $storageRepository->findOneBy(["id" => $id]);

        return $this->render('storage/storage.html.twig', [
            'controller_name' => 'StorageController',
            'storage' => $storage
        ]);
    }

    #[Route('/new', name: 'app_storage_new', methods: ['POST'])]
    public function newStorage(EntityManagerInterface $entityManager, Request $request): Response
    {
        $storage = new Storage();
        $form = $this->createForm(StorageType::class, $storage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($storage);
            $entityManager->flush();
        }

        return $this->render('storage/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
