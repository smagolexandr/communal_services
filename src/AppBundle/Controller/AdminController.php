<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Order;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/admin")
 */
class AdminController extends Controller
{
    /**
     * @Route("/", name="admin_index")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $orders = $em->getRepository(Order::class)->findBy(['status' => 'new']);

        return [
            'orders' => $orders
        ];
    }

    /**
     * @Route("/order/{id}/review", name="order_review")
     * @Template()
     */
    public function orderReviewAction(Request $request, Order $order)
    {
        $reviewForm = $this->createForm('AppBundle\Form\OrderReviewType', $order);
        $reviewForm->handleRequest($request);

        if ($reviewForm->isSubmitted() && $reviewForm->isValid()) {
            $order->setStatus(Order::STATUS_PENDING);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_index');
        }

        return [
            'order' => $order,
            'form' => $reviewForm->createView()
        ];
    }
}
