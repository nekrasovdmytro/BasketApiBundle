<?php
/**
 * Created by PhpStorm.
 * User: nekrasov
 * Date: 15.04.16
 * Time: 18:35
 */

namespace Binary\Bundle\FruitBasketApiBundle\Rest;

use Symfony\Component\Validator\ConstraintViolationList;

class RestValidationException extends \Exception
{
    /**
     * @var $validationData ConstraintViolationList
    */
    protected $validationData;

    public function __construct(ConstraintViolationList $data, $message = '', $code = 0, Exception $previous = null)
    {
        $this->validationData = $data;
        parent::__construct($message, $code, $previous);
    }

    public function getValidationData()
    {
        return $this->validationData;
    }
}