<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/user')]
class UserController extends AbstractController
{
    #[Route('/', name: 'app_user', methods: ['GET'])]
    public function index(Security $security, UserRepository $userRepository): Response
    {
        $username = $security->getUser()->getUserIdentifier();
        $user = $userRepository->findOneBy(['username' => $username]);

        if (isset($user)) {
            return $this->render('user/index.html.twig', [
                'user' => $user,
            ]);
        }

        throw $this->createNotFoundException('User not found');
    }
}
