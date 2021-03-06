<?php

namespace App\Controller;

use App\Entity\MicroPost;
use App\Entity\User;
use App\Form\MicroPostType;
use App\Repository\MicroPostRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/micro-post")
 *
 * Class BlogController
 * @package App\Controller
 */
class MicroPostController extends AbstractController
{
    private $microPostRepository;
    private $entityManager;
    private $flashBag;
    private $userRepository;

    public function __construct(MicroPostRepository $microPostRepository, EntityManagerInterface $entityManager, FlashBagInterface $flashBag, UserRepository $userRepository)
    {
        $this->microPostRepository = $microPostRepository;
        $this->entityManager = $entityManager;
        $this->flashBag = $flashBag;
        $this->userRepository = $userRepository;
    }

    /**
     * @Route("/", name="micro_post_index")
     */
    public function index()
    {
        $currentUser = $this->getUser();

        $usersToFollow = [];

        if ($currentUser instanceof User) {
            $post = $this->microPostRepository->findAllByUser($currentUser->getFollowing());
            $usersToFollow = count($post) === 0 ? $this->userRepository->findAllWithMoreThanFivePostsExceptUser($currentUser) : [];
        } else {
            $post = $this->microPostRepository->findBy([], ['time' => 'DESC']);
        }

        return $this->render('micro-post/index.html.twig', [
            'posts' => $post,
            'userToFollow' => $usersToFollow
        ]);
    }

    /**
     * @Route("/edit/{id}", name="micro_post_edit")
     *
     * @param MicroPost $microPost
     * @param Request $request
     * @return Response
     */
    public function edit(MicroPost $microPost, Request $request)
    {
        $this->denyAccessUnlessGranted('edit', $microPost);

        $form = $this->createForm(MicroPostType::class, $microPost);
        $form->handleRequest($request);

        $microPost->setTime(new \DateTime());

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $this->entityManager->flush();

                $this->flashBag->add('warning', 'Micro post was updated');

                return $this->redirectToRoute('micro_post_index');
            }
        }

        return $this->render('micro-post/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/delete/{id}", name="micro_post_delete")
     *
     * @param MicroPost $microPost
     */
    public function delete(MicroPost $microPost)
    {
        $this->denyAccessUnlessGranted('delete', $microPost);

        $this->entityManager->remove($microPost);
        $this->entityManager->flush();

        $this->flashBag->add('error', 'Micro post was deleted');

        return $this->redirectToRoute('micro_post_index');
    }

    /**
     * @Route("/add", name="micro_post_add")
     * @Security("is_granted('ROLE_USER')")
     */
    public function add(Request $request)
    {
        $microPost = (new MicroPost())
            //->setTime(new \DateTime())
            ->setUser($this->getUser());

        $form = $this->createForm(MicroPostType::class, $microPost);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $em = $this->entityManager;
                $em->persist($microPost);
                $em->flush();

                $this->flashBag->add('success', 'Micro post was created');

                return $this->redirectToRoute('micro_post_index');
            }
        }

        return $this->render('micro-post/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/user/{username}", name="micro_post_user")
     */
    public function userPosts(User $user)
    {
        return $this->render('micro-post/user-post.html.twig', [
//            'posts' => $this->microPostRepository->findBy(['user' => $user], ['time' => 'DESC'])
            'posts' => $user->getMicroPosts(),
            'user' => $user
        ]);
    }

    /**
     * @Route("/{id}", name="micro_post_show")
     *
     * @param MicroPost $microPost
     * @return Response
     */
    public function show(MicroPost $microPost)
    {
        return $this->render('micro-post/show.html.twig', [
            'post' => $microPost
        ]);
    }
}
