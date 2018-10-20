<?php

namespace App\Controller;

use App\Entity\MicroPost;
use App\Form\MicroPostType;
use App\Repository\MicroPostRepository;
use Doctrine\ORM\EntityManagerInterface;
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

    public function __construct(MicroPostRepository $microPostRepository, EntityManagerInterface $entityManager, FlashBagInterface $flashBag)
    {
        $this->microPostRepository = $microPostRepository;
        $this->entityManager = $entityManager;
        $this->flashBag = $flashBag;
    }

    /**
     * @Route("/", name="micro_post_index")
     */
    public function index()
    {
        return $this->render('micro-post/index.html.twig', [
            'posts' => $this->microPostRepository->findBy([], ['time' => 'DESC'])
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
        $this->entityManager->remove($microPost);
        $this->entityManager->flush();

        $this->flashBag->add('danger', 'Micro post was deleted');

        return $this->redirectToRoute('micro_post_index');
    }

    /**
     * @Route("/add", name="micro_post_add")
     */
    public function add(Request $request)
    {
        $microPost = (new MicroPost())
            ->setTime(new \DateTime());

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
