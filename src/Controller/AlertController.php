<?php

namespace App\Controller;

use App\Entity\Alert;
use App\Form\AlertType;
use App\Repository\AlertRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/alerts')]
class AlertController extends AbstractController
{
    #[Route('/', name: 'app_alerts', methods: ['GET'])]
    public function index(AlertRepository $alertRepository): Response
    {
        $alerts = $alertRepository->findAll();

        return $this->render('alert/index.html.twig', [
            'alerts' => $alerts,
        ]);
    }

    #[Route('/new', name: 'app_alert_new', methods: ['GET', 'POST'])]
    public function new(EntityManagerInterface $entityManager, Request $request): Response
    {
        $alert = new Alert();

        $form = $this->createForm(AlertType::class, $alert);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $entityManager->persist($alert);
                $entityManager->flush();

                $this->addFlash('success', 'Alert created successfully');
                return $this->redirectToRoute('app_alert_new');
            } else {
                $error = $form->getErrors()[0]->getMessage();
                $this->addFlash('danger', $error);
                return $this->redirectToRoute('app_alert_new');
            }
        }

        return $this->render('alert/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/delete/{id}', name: 'app_alert_delete', methods: ['DELETE'])]
    public function delete(EntityManagerInterface $entityManager, int $id): Response
    {
        $alert = $entityManager->getRepository(Alert::class)->findOneBy(['id' => $id]);

        if (isset($alert)) {
            $entityManager->remove($alert);
            $entityManager->flush();

            return new Response("Alert removed successfully", Response::HTTP_OK);
        }

        throw $this->createNotFoundException('Alert not found');
    }
}
