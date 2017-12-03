<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Order;
use AppBundle\Entity\User;
use AppBundle\Form\OrderCompleteType;
use AppBundle\Form\UserType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/user")
 */
class UserController extends Controller
{
    /**
     * @Route("/", name="user_index")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $orders = $em->getRepository(Order::class)->findBy(['user' => $this->getUser()], ['id' => 'DESC']);

        return [
            'orders' => $orders
        ];
    }

    /**
     * @Route("/order/{id}/confirm", name="order_confirm")
     */
    public function orderConfirmAction(Request $request, Order $order)
    {
        $em = $this->getDoctrine()->getManager();
        if ($request->query->get('accepted') == TRUE) {
            $order->setStatus(Order::STATUS_ACCEPTED);
        } else {
            $em->remove($order);
        }
        $em->flush();

        return $this->redirectToRoute('user_index');
    }

    /**
     * @Route("/order/{id}/complete", name="order_complete")
     * @Template()
     */
    public function orderCompleteAction(Request $request, Order $order)
    {
        $form = $this->createForm(OrderCompleteType::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $order->setStatus(Order::STATUS_COMPLETED);

            $em->flush();

            return $this->redirectToRoute('user_index');
        }

        return [
            'order' => $order,
            'form' => $form->createView(),
        ];
    }
}
