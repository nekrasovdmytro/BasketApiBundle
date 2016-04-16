<?php

namespace Binary\Bundle\FruitBasketApiBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="basket")
 */
class Basket implements \JsonSerializable
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank(message="name can't be empty")
     */
    private $name;

    /**
     * @ORM\Column(type="decimal", scale=2)
     * /**
     * @Assert\GreaterThan(
     *     value = 0,
     *     message = "Max value of capacity should be greater than 0"
     * )
     */
    private $maxCapacity;

    /**
     * @ORM\OneToMany(targetEntity="Item" , cascade={"remove"}, mappedBy="basket")
     */
    private $contents;

    public function __construct()
    {
        $this->contents = new ArrayCollection();
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
     * Set name
     *
     * @param string $name
     * @return Basket
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set maxCapacity
     *
     * @param string $maxCapacity
     * @return Basket
     */
    public function setMaxCapacity($maxCapacity)
    {
        $this->maxCapacity = $maxCapacity;

        return $this;
    }

    /**
     * Get maxCapacity
     *
     * @return string 
     */
    public function getMaxCapacity()
    {
        return $this->maxCapacity;
    }

    /**
     * Add contents
     *
     * @param \Binary\Bundle\FruitBasketApiBundle\Entity\Item $contents
     * @return Basket
     */
    public function addContent(\Binary\Bundle\FruitBasketApiBundle\Entity\Item $contents)
    {
        $this->contents[] = $contents;

        return $this;
    }

    /**
     * Remove contents
     *
     * @param \Binary\Bundle\FruitBasketApiBundle\Entity\Fruit $contents
     */
    public function removeContent(\Binary\Bundle\FruitBasketApiBundle\Entity\Fruit $contents)
    {
        $this->contents->removeElement($contents);
    }

    /**
     * Get contents
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getContents()
    {
        return $this->contents;
    }

    public function toArray()
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'maxCapacity' => $this->getMaxCapacity(),
            'contents' => $this->getContents()->toArray()
        ];
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
