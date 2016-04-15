<?php

namespace Binary\Bundle\FruitBasketApiBundle\Rest\JsonResponse;

use Binary\Bundle\FruitBasketApiBundle\Rest\RestStatus;
use Symfony\Component\HttpFoundation\JsonResponse as HttpFoundationJsonResponse;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Form\FormErrorIterator;


class JsonResponse extends HttpFoundationJsonResponse
{
    /**
     * @var JsonArray $array
    */
    protected $jsonArray;

    public function __construct($data = null, $status = 200, $headers = array())
    {
        $this->jsonArray = new JsonArray();

        $this->handleResponse($data, $status);

        parent::__construct($this->jsonArray->getArray(), $status, $headers);
    }

    /**
     * @param Mixed $data
     * @param integer $status
    */
    protected function handleResponse($data, &$status)
    {
        // validator
        if ($data instanceof ConstraintViolationList) {
            $this->jsonArray->setStatus(RestStatus::ERROR_STATUS);
            $this->prepareValidationErrorResponse($data);
            $status = HttpFoundationJsonResponse::HTTP_BAD_REQUEST;

        } elseif($data instanceof FormErrorIterator) {
            $this->jsonArray->setStatus(RestStatus::ERROR_STATUS);
            $this->prepareFormErrorResponse($data);
            $status = HttpFoundationJsonResponse::HTTP_BAD_REQUEST;

        } elseif ($data instanceof \Exception) {
            $this->jsonArray->setStatus(RestStatus::ERROR_STATUS);
            $this->prepareExceptionErrorResponse($data);

        } else {
            $this->jsonArray->setData($data);
        }
    }

    protected function prepareValidationErrorResponse(ConstraintViolationList $data)
    {
        foreach($data as $item) {
            $this->jsonArray->setData($item->getMessage());
        }
    }

    protected function prepareFormErrorResponse(FormErrorIterator $data)
    {
        foreach($data as $item) {
            $this->jsonArray->setData($item->getMessage());
        }
    }

    protected function prepareExceptionErrorResponse(\Exception $data)
    {
        $this->jsonArray->setData($data->getMessage());
    }
}