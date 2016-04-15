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
     */
    public function addBasketAction(Request $request)
    {
        $basket = new Basket();

        $form = $this->createForm(BasketType::class, $basket);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getEntitiesManager();
            $em->persist($basket);
            $em->flush();

            $data = $basket;
        } else {
            $data = $form->getErrors(true);
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
        $basket = $this->getBasketRepository()
            ->findOneById($id);

        return new JsonResponse($basket);
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
                throw new \Exception("Basket not found, wrong basket");
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
                    throw new \Exception("Type not found, wrong type");
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

            return new JsonResponse(sprintf("Added %s item(s)", count($itemArray['data'])));

        } catch (RestValidationException $e) {
            return new JsonResponse($e);
        } catch (\Exception $e) {
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