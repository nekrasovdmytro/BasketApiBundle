<?php
/**
 * Created by PhpStorm.
 * User: nekrasov
 * Date: 16.04.16
 * Time: 10:52
 */

namespace Binary\Bundle\FruitBasketApiBundle\EventListener;

use Binary\Bundle\FruitBasketApiBundle\Rest\JsonResponse\JsonResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class RouteNotFoundListener
{
    /**
     *@var Router
     */
    protected $router;

    /**
     * @param Router
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }


    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();

        if ($exception instanceof NotFoundHttpException || //route is not found
            $exception instanceof MethodNotAllowedHttpException) { //route is fount but method is not allowed

            $response = new JsonResponse($exception);
            $event->setResponse($response);

        }
    }
}
