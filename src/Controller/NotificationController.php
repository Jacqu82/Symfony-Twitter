<?php

namespace App\Controller;

use App\Repository\NotificationRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/notification")
 * @Security("is_granted('ROLE_USER')")
 *
 * Class NotificationController
 * @package App\Controller
 */
class NotificationController extends AbstractController
{
    private $notificationRepository;

    public function __construct(NotificationRepository $notificationRepository)
    {
        $this->notificationRepository = $notificationRepository;
    }

    /**
     * @Route("/unread-count", name="notification_unread")
     */
    public function unreadCount()
    {
        return new JsonResponse([
            'count' => $this->notificationRepository->findUnseenByUser($this->getUser())
        ]);
    }

    /**
     * @Route("/index", name="notification_index")
     */
    public function index()
    {
        return $this->render('notification/index.html.twig', [
            'notifications' => $this->notificationRepository->findBy(['seen' => false, 'user' => $this->getUser()])
        ]);
    }
}
