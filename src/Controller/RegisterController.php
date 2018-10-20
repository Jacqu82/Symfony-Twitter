<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegisterController extends AbstractController
{
    private $userRepository;
    private $entityManager;
    private $flashBag;
    private $passwordEncoder;

    public function __construct(UserRepository $userRepository, EntityManagerInterface $entityManager, FlashBagInterface $flashBag, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
        $this->flashBag = $flashBag;
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @Route("/register", name="user_register")
     *
     * @param Request $request
     * @return Response
     */
    public function register(Request $request)
    {
        $user = (new User())
            ->setCreatedAt(new \DateTime());

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $user->setPassword($this->passwordEncoder->encodePassword($user, $user->getPlainPassword()));
                $this->entityManager->persist($user);
                $this->entityManager->flush();
                $this->flashBag->add('success', 'Account has been successfully created :)');

                return $this->redirectToRoute('security_login');
            }
        }

        return $this->render('user/register.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
