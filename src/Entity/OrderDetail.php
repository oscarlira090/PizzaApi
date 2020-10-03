<?php

namespace App\Entity;

use App\Repository\OrderDetailRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OrderDetailRepository::class)
 */
class OrderDetail
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $orderId;

    /**
     * @ORM\Column(type="integer")
     */
    private $pizzaId;

    /**
     * @ORM\Column(type="integer")
     */
    private $quantity;

    /**
     * @ORM\Column(type="float")
     */
    private $subtotal;

    /**
     * @ORM\Column(type="float")
     */
    private $price;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Order", inversedBy="OrderDetail")
     */
    private $order;

      /**
     * @ORM\ManyToMany(targetEntity="Topping", inversedBy="OrderDetail")
     * @ORM\JoinTable(name="order_detail_topping",
     *      joinColumns={@ORM\JoinColumn(name="order_detail_id", referencedColumnName="id", onDelete="cascade")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="topping_id", referencedColumnName="id")}
     *      )
     */

    private $toppings;


    public function __construct() {
        $this->toppings = new ArrayCollection();
    }

    public function getOrder(): ?Order
    {
        return $this->order;
    }

    public function setOrder(?Order $order): self
    {
        $this->order = $order;

        return $this;
    }

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Pizza", inversedBy="OrderDetail")
     */
    private $pizza;

    public function setPizza(?Pizza $pizza): self
    {
        $this->pizza = $pizza;

        return $this;
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrderId(): ?int
    {
        return $this->OrderId;
    }

    public function setOrderId(int $OrderId): self
    {
        $this->OrderId = $OrderId;

        return $this;
    }

    public function getPizzaId(): ?int
    {
        return $this->PizzaId;
    }

    public function setPizzaId(int $PizzaId): self
    {
        $this->PizzaId = $PizzaId;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getSubtotal(): ?float
    {
        return $this->subtotal;
    }

    public function setSubtotal(float $subtotal): self
    {
        $this->subtotal = $subtotal;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }


    public function addTopping(Topping $top): self
    {
        //$top->addOrderDetail($this); // synchronously updating inverse side
        $this->toppings->add($top);
        return $this;
    }
}
