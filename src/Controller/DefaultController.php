<?php

namespace App\Controller;

use App\Service\Greeting;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController
{
    private $greeting;
    private $twig;

    public function __construct(Greeting $greeting, \Twig_Environment $twig)
    {
        $this->greeting = $greeting;
        $this->twig = $twig;
    }

    /**
     * @Route("/hello/{name}", name="hello", defaults={"name"="Jacek"})
     *
     * @param $name
     * @return Response
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function index(Request $request)
    {
        $html = $this->twig->render('default/index.html.twig', [
            'message' => $this->greeting->greet($request->attributes->get('name'))]);

        return new Response($html);
    }

//    /**
//     * @Route("/_error/404", name="error_page")
//     */
//    public function showException()
//    {
//        $html = $this->twig->render('bundles/TwigBundle/Exception/error404.html.twig');
//
//        return new Response($html);
//    }
}
