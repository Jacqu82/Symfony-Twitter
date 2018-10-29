<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{

    private $authenticationUtils;
    private $userRepository;
    private $entityManager;

    public function __construct(AuthenticationUtils $authenticationUtils, UserRepository $userRepository, EntityManagerInterface $entityManager)
    {
        $this->authenticationUtils = $authenticationUtils;
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/login", name="security_login")
     */
    public function login()
    {
        return $this->render(
            'security/login.html.twig', [
                'last_username' => $this->authenticationUtils->getLastUsername(),
                'error' => $this->authenticationUtils->getLastAuthenticationError()
            ]
        );
    }

    /**
     * @Route("/confirm/{token}", name="security_confirm")
     */
    public function confirm($token)
    {
        $user = $this->userRepository->findOneBy([
            'confirmationToken' => $token
        ]);

        if (null !== $user) {
            $user->setEnabled(true);
            $user->setConfirmationToken('');
            $this->entityManager->flush();
        }

        return $this->render('security/confirmation.html.twig', [
            'user' => $user
        ]);
    }
}