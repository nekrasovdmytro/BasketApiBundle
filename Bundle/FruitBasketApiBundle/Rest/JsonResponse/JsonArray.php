<?php

namespace Binary\Bundle\FruitBasketApiBundle\Rest\JsonResponse;

use Binary\Bundle\FruitBasketApiBundle\Rest\RestStatus;

class JsonArray
{
    /**
     * @var array $array
     */
    protected $array;

    /**
     * @var mixed $data
     */
    protected $data;

    public function __construct($status = RestStatus::OK_STATUS)
    {
        $this->array = ['status' => $status, 'data' => []];
    }

    public function setStatus($status = RestStatus::OK_STATUS)
    {
        $this->array['status'] = $status;
    }

    /**
     * @param mixed $data
    */
    public function setData($data)
    {
        $this->data[] = $data;
    }

    /**
     * @return array $data
     */
    public function getArray()
    {
        $this->prepareArray();

        return $this->array;
    }

    protected function prepareArray()
    {
        $this->array['data'] = $this->data;
    }
}