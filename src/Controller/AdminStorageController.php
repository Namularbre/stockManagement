<?php

namespace App\Controller;

use App\Entity\Storage;
use App\Form\AdminStorageType;
use App\Repository\StorageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/storage')]
class AdminStorageController extends AbstractController
{
    #[Route('/', name: 'app_admin_storage_index', methods: ['GET'])]
    public function index(StorageRepository $storageRepository): Response
    {
        return $this->render('admin_storage/index.html.twig', [
            'storages' => $storageRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_admin_storage_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $storage = new Storage();
        $form = $this->createForm(AdminStorageType::class, $storage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($storage);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_storage_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin_storage/new.html.twig', [
            'storage' => $storage,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_storage_show', methods: ['GET'])]
    public function show(Storage $storage): Response
    {
        return $this->render('admin_storage/show.html.twig', [
            'storage' => $storage,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_storage_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Storage $storage, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AdminStorageType::class, $storage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_storage_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin_storage/edit.html.twig', [
            'storage' => $storage,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_storage_delete', methods: ['POST'])]
    public function delete(Request $request, Storage $storage, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$storage->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($storage);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_storage_index', [], Response::HTTP_SEE_OTHER);
    }
}
