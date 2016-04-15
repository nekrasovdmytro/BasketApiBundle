<?php

namespace Binary\Bundle\FruitBasketApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="item")
 * @ORM\HasLifecycleCallbacks()
 */
class Item implements \JsonSerializable
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Type")
     * @ORM\JoinColumn(name="type_id", referencedColumnName="id")
     */
    private $type = null;

    /**
     * @ORM\Column(type="decimal", scale=2)
     * @Assert\GreaterThan(
     *     value = 0,
     *     message = "Weight should be greater than 0"
     * )
     */
    private $weight;

    /**
     * @ORM\ManyToOne(targetEntity="Basket", inversedBy="items")
     * @ORM\JoinColumn(name="basket_id", referencedColumnName="id")
     */
    private $basket = null;

    /**
     * @ORM\PreFlush
     */
    public function checkBasketCapacity()
    {
        $basket = $this->getBasket();
        $maxCapacity = $basket->getMaxCapacity();

        /**
         * @var \Doctrine\Common\Collections\Collection $items
        */
        $items = $basket->getContents();

        $capacity = 0;
        foreach($items as $item) {
            $capacity += $item->getWeight();
        }

        if ($capacity > $maxCapacity) {
            throw new \Exception("Capacity is out of range");
        }
    }

    /**
     * @Assert\IsTrue(message = "The password cannot match your first name")
     */
    public function isTypeAndBasketSet()
    {
        $isBasketOrTypeNull = $this->getBasket() === null ||
            $this->getType() === null;

       return !$isBasketOrTypeNull;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set weight
     *
     * @param string $weight
     * @return Item
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;

        return $this;
    }

    /**
     * Get weight
     *
     * @return string 
     */
    public function getWeight()
    {
        return $this->weight;
    }


    /**
     * Get type
     *
     * @return \Binary\Bundle\FruitBasketApiBundle\Entity\Type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set basket
     *
     * @param \Binary\Bundle\FruitBasketApiBundle\Entity\Basket $basket
     * @return Item
     */
    public function setBasket(\Binary\Bundle\FruitBasketApiBundle\Entity\Basket $basket = null)
    {
        $this->basket = $basket;

        return $this;
    }

    /**
     * Get basket
     *
     * @return \Binary\Bundle\FruitBasketApiBundle\Entity\Basket 
     */
    public function getBasket()
    {
        return $this->basket;
    }

    public function toArray()
    {
        return [
            'id' => $this->getId(),
            'type' => $this->getType(),
            'weight' => $this->getWeight()
        ];
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * Set type
     *
     * @param \Binary\Bundle\FruitBasketApiBundle\Entity\Type $type
     * @return Item
     */
    public function setType(\Binary\Bundle\FruitBasketApiBundle\Entity\Type $type = null)
    {
        $this->type = $type;

        return $this;
    }
}
