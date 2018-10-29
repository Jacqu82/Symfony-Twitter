<?php

namespace App\Controller;

use App\Entity\User;
use App\Event\UserRegisterEvent;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Security\TokenGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
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
    private $eventDispatcher;
    private $tokenGenerator;

    public function __construct(
        UserRepository $userRepository,
        EntityManagerInterface $entityManager,
        FlashBagInterface $flashBag,
        UserPasswordEncoderInterface $passwordEncoder,
        EventDispatcherInterface $eventDispatcher,
        TokenGenerator $tokenGenerator
    )
    {
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
        $this->flashBag = $flashBag;
        $this->passwordEncoder = $passwordEncoder;
        $this->eventDispatcher = $eventDispatcher;
        $this->tokenGenerator = $tokenGenerator;
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
                $user->setConfirmationToken($this->tokenGenerator->getRandomSecureToken(30));
                $this->entityManager->persist($user);
                $this->entityManager->flush();
                $this->flashBag->add('success', 'Account has been successfully created :)');

                $userRegisterEvent = new UserRegisterEvent($user);
                $this->eventDispatcher->dispatch($userRegisterEvent::NAME, $userRegisterEvent);

                return $this->redirectToRoute('security_login');
            }
        }

        return $this->render('user/register.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
