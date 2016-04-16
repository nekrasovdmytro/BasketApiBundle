<?php

namespace Binary\Bundle\FruitBasketApiBundle\Controller;

use Binary\Bundle\FruitBasketApiBundle\Entity\Item;
use Binary\Bundle\FruitBasketApiBundle\Rest\RestValidationException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Binary\Bundle\FruitBasketApiBundle\Rest\JsonResponse\JsonResponse;
use Binary\Bundle\FruitBasketApiBundle\Form\BasketType;
use Binary\Bundle\FruitBasketApiBundle\Entity\Basket;
use Symfony\Component\Validator\Constraints as Assert;

class FBApiController extends Controller
{
    /**
     * @Route("/", name="index_action")
     */
    public function indexAction()
    {
        return new JsonResponse(array());
    }

    /**
     * @Route("/add-basket", name="add_basket_action")
     * @Method("POST")
     *
     * Data Request Example
     * POST api.example.local
     * Content-type="application/json"
     * {"name":"Basket 1", "maxCapacity":"15"}
     */
    public function addBasketAction(Request $request)
    {
        /**
         * @var \Symfony\Component\Validator\Validator $validator
         */
        $validator = $this->get("validator");

        if ($request->getContentType() == 'application/json') {
            $itemArray = json_decode($request->getContent(), true);
        } else {
            $itemArray = json_decode($request->get('data'), true);
        }

        $basket = new Basket();

        $basket->setName(!empty($itemArray['name']) ? $itemArray['name'] : '' );
        $basket->setMaxCapacity(!empty($itemArray['maxCapacity']) ? $itemArray['maxCapacity'] : 0 );

        $errors = $validator->validate($basket);

        if (!count($errors)) {
            $em = $this->getEntitiesManager();
            $em->persist($basket);
            $em->flush();

            $data = $basket;
        } else {
            $data = $errors;
        }

        return new JsonResponse($data, JsonResponse::HTTP_CREATED);
    }

    /**
     * @Route("/view-baskets", name="view_baskets_action")
     * @Method({"GET", "HEAD"})
     */
    public function viewBasketsListAction(Request $request)
    {
        $data = $this->getBasketRepository()
            ->findAll();

        return new JsonResponse($data);
    }

    /**
     * @Route("/view-baskets/{id}", name="view_basket_id_action")
     * @Method({"GET", "HEAD"})
     */
    public function viewBasketByIdAction(Request $request, $id)
    {
        try {
            $basket = $this->getBasketRepository()
                ->findOneById($id);

            if (!$basket) {
                throw new \Exception("Basket not found, wrong id", JsonResponse::HTTP_NOT_FOUND);
            }

            return new JsonResponse($basket);
        } catch (\Exception $e) {
            return new JsonResponse($e);
        }
    }

    /**
     * @Route("/add-item/", name="add_item_action"
     * )
     * @Method("POST")
     * Json example like a POST var POST[data]=
     * {
     * "basket":"13",
     * "data":[
     *     {"weight":"20", "type":"apple" },
     *     {"weight":"10", "type":"apple" }
     * ]
     * }
     */
    public function addItemsAction(Request $request)
    {
        try {
            $em = $this->getEntitiesManager();

            /**
             * @var \Symfony\Component\Validator\Validator $validator
            */
            $validator = $this->get("validator");

            if ($request->getContentType() == 'application/json') {
                $itemArray = json_decode($request->getContent(), true);
            } else {
                $itemArray = json_decode($request->get('data'), true);
            }

            if (empty($itemArray['basket'])) {
                throw new \Exception("basket is not set");
            }

            /**
             * @var \Binary\Bundle\FruitBasketApiBundle\Entity\Basket $basket
            */
            $basket = $this->getBasketRepository()
                ->findOneById($itemArray['basket']);

            if (!$basket) {
                throw new \Exception("Basket not found, wrong basket", JsonResponse::HTTP_NOT_FOUND);
            }

            foreach ($itemArray['data'] as $data) {
                $item = new Item();

                if (empty($data['type'])) {
                    throw new \Exception("basket is not set");
                }
                /**
                 * @var \Binary\Bundle\FruitBasketApiBundle\Entity\Type $type
                 */
                $type = $this->getTypeRepository()
                    ->findOneByName($data['type']);

                if (!$type) {
                    throw new \Exception("Type not found, wrong type", JsonResponse::HTTP_NOT_FOUND);
                }

                $item->setWeight($data['weight']);
                $item->setBasket($basket);
                $item->setType($type);

                $errors = $validator->validate($item);
                if (count($errors)) {
                    throw new RestValidationException($errors);
                }

                // add item to basket to calculate weight of all
                $basket->addContent($item);

                $em->persist($item);
            }

            $em->flush();

            return new JsonResponse($basket, JsonResponse::HTTP_CREATED);

        } catch (RestValidationException $e) {
            return new JsonResponse($e);
        } catch (\Exception $e) {
            return new JsonResponse($e);
        }
    }

    /**
     * @Route("/basket/delete/{id}", name="delete_basket_action")
     * @Method("DELETE")
     */
    public function removeBasketAction(Request $request, $id)
    {
        try {
            $em = $this->getEntitiesManager();

            /**
             * @var \Binary\Bundle\FruitBasketApiBundle\Entity\Basket $basket
             */
            $basket = $this->getBasketRepository()
                ->findOneById($id);

            if (!$basket) {
                throw new \Exception("Basket not found, wrong id", JsonResponse::HTTP_NOT_FOUND);
            }

            $em->remove($basket);
            $em->flush();

            return new JsonResponse(sprintf("Basket %s deleted", $id));
        }  catch (\Exception $e) {
            return new JsonResponse($e);
        }
    }

    protected function getEntitiesManager()
    {
        return $this->getDoctrine()
            ->getManager();
    }

    protected function getBasketRepository()
    {
        return $this->getDoctrine()
            ->getRepository("BasketApiBundle:Basket");
    }

    protected function getTypeRepository()
    {
        return $this->getDoctrine()
            ->getRepository("BasketApiBundle:Type");
    }
}