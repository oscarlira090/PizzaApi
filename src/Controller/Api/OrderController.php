<?php

namespace App\Controller\Api;

use App\Entity\Order;
use App\Entity\OrderDetail;
use App\Repository\OrderDetailRepository;
use App\Repository\OrderRepository;
use App\Repository\PizzaRepository;
use App\Repository\ToppingRepository;
use DateTime;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use FOS\RestBundle\Request\ParamFetcher;


class OrderController extends AbstractFOSRestController
{
    private $orderRepository;
    private $orderDetailRepository;
    private $pizzaRepository;
    private $toppingRepository;
    const PENDIENTE = 1;
    const PAID = 2;

    public function __construct(OrderRepository $orderRepository, OrderDetailRepository $orderDetailRepository, PizzaRepository $pizzaRepository, ToppingRepository $toppingRepository)
    {
        $this->orderRepository = $orderRepository;
        $this->orderDetailRepository = $orderDetailRepository;
        $this->pizzaRepository = $pizzaRepository;
        $this->toppingRepository = $toppingRepository;
    }

    /**
     * @Rest\Get(path="/orders")
     * @Rest\View(serializerGroups={"order"}, serializerEnableMaxDepthChecks=true)
     */
    public function getOrders()
    {
        $orders = $this->orderRepository->findAll();
        return View::create($orders, Response::HTTP_OK);
    }

    /**
     * @Rest\Get(path="/orders/{id}/status")
     * @Rest\View(serializerGroups={"order"}, serializerEnableMaxDepthChecks=true)
     */
    public function getOrderStatus(int $id)
    {
        $order = $this->orderRepository->findOneBy(['id' => $id]);
        if ($order == null)
            return View::create(['message' => 'Order not found'], Response::HTTP_NOT_FOUND);
        return View::create(['status' => $this->getStatus($order->getStatus())], Response::HTTP_OK);
    }

    /**
     * @Rest\Post(path="/orders")
     * @Rest\View(serializerGroups={"order"}, serializerEnableMaxDepthChecks=true)
     */
    public function postOrder(Request $request)
    {

        $data = json_decode($request->getContent(), true);
        $order = new Order();
        $order->setCustomerId((int)$data['order']['customer']);
        $order->setCreatedAt(new DateTime());
        $order->setUpdatedAt(new DateTime());
        $order->setStatus(self::PENDIENTE);
        $total = 0;

        foreach ($data['order']['pizzas'] as $pizza) {
            $pizzaORm = $this->pizzaRepository->findOneBy(['id' => $pizza['id']]);

            if ($pizzaORm == null)
                return View::create(['message' => 'Pizza not found: try again'], Response::HTTP_BAD_REQUEST);

            foreach ($pizza['toppings'] as $top) {
                $topORm = $this->toppingRepository->findOneBy(['id' => $top]);
                if ($topORm == null)
                    return View::create(['message' => 'Topping not found: try again'], Response::HTTP_BAD_REQUEST);
            }
            $this->saveItems($total, $pizza, $order, $pizzaORm, $pizza['toppings']);
        }
        $order->setTotal($total);
        $order = $this->orderRepository->saveOrder($order);

        return View::create($order, Response::HTTP_OK);
    }

    /**
     * @Rest\Put(path="/orders/{id}")
     * @Rest\View(serializerGroups={"order"}, serializerEnableMaxDepthChecks=true)
     */
    public function putOrder(int $id, Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $order = $this->orderRepository->findOneBy(['id' => $id]);

        if ($order == null)
            return View::create(['message' => 'Order not found'], Response::HTTP_NOT_FOUND);

        $order->setUpdatedAt(new DateTime());
        $total = 0;
        $this->orderDetailRepository->deleteByOrderId($id);

        foreach ($data['order']['pizzas'] as $pizza) {

            $pizzaORm = $this->pizzaRepository->findOneBy(['id' => $pizza['id']]);
            if ($pizzaORm == null)
                return View::create(['message' => 'Pizza not found: try again'], Response::HTTP_BAD_REQUEST);

            foreach ($pizza['toppings'] as $top) {
                $topORm = $this->toppingRepository->findOneBy(['id' => $top]);
                if ($topORm == null)
                    return View::create(['message' => 'Topping not found: try again'], Response::HTTP_BAD_REQUEST);
            }

            $this->saveItems($total, $pizza, $order, $pizzaORm, $pizza['toppings']);
        }

        $order->setTotal($total);
        $order = $this->orderRepository->saveOrder($order);
        return View::create($order, Response::HTTP_OK);
    }

    public function saveItems(&$total, $item, &$order, $pizzaORm, $toppings)
    {
        $orderDetail = new OrderDetail();
        $orderDetail->setQuantity($item['quantity']);
        $orderDetail->setPrice($item['price']);
        $orderDetail->setSubtotal($orderDetail->getQuantity() * $orderDetail->getPrice());
        $total += $orderDetail->getSubtotal();
        $orderDetail->setPizza($pizzaORm);
        $order->addPizza($orderDetail);

        foreach ($toppings as $top) {
            $topORm = $this->toppingRepository->findOneBy(['id' => $top]);
            $orderDetail->addTopping($topORm);
        }
    }

    public function getStatus($status)
    {
        switch ($status) {
            case self::PENDIENTE:
                return  'PENDIENTE';
            case self::PAID:
                return  'PAID';
        }
    }
}
