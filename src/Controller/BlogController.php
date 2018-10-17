<?php

namespace App\Controller;

use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/blog")
 *
 * Class BlogController
 * @package App\Controller
 */
class BlogController extends AbstractController
{
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @Route("/", name="blog_index")
     */
    public function index()
    {
        //$this->session->remove('posts');
        return $this->render('blog/index.html.twig', [
            'posts' => $this->session->get('posts')
        ]);
    }

    /**
     * @Route("/add", name="blog_add")
     */
    public function add()
    {
        $posts = $this->session->get('posts');
        $posts[uniqid()] = [
            'title' => 'A random title ' . rand(1, 500),
            'text' => 'Some random text nr ' . rand(1, 500),
            'date' => new DateTime()
        ];

        $this->session->set('posts', $posts);

        return new RedirectResponse($this->generateUrl('blog_index'));
    }

    /**
     * @Route("/show/{id}", name="blog_show")
     *
     * @param $id
     */
    public function show($id)
    {
        $posts = $this->session->get('posts');

        if (!$posts || !isset($posts[$id])) {
            throw new NotFoundHttpException('Post not found!');
        }

        return $this->render('blog/show.html.twig', [
            'id' => $id,
            'post' => $posts[$id]
        ]);
    }
}
