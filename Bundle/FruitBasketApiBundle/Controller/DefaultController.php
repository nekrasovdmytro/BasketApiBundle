<?php

namespace Binary\Bundle\FruitBasketApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('BasketApiBundle:Default:index.html.twig', array('name' => $name));
    }
}
