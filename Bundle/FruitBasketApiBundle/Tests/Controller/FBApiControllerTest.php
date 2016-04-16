<?php
/**
 * Created by PhpStorm.
 * User: nekrasov
 * Date: 16.04.16
 * Time: 11:08
 */

namespace Binary\Bundle\FruitBasketApiBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FBApiControllerTest extends WebTestCase
{
    protected $content = [
        'addBasket' => ["name" => "Basket 1", "maxCapacity" => "75"]
    ];

    protected $expected = [
        'addBasket' => '/\{"status":"ok","data":\[\{"id":\d+,"name":"Basket 1","maxCapacity":"75","contents":\[\]\}\]\}/'
    ];

    public function testAddBasketAction()
    {
        $client = static::createClient();

        $url = $client->getContainer()->get('router')->generate('add_basket_action');

        $client->request('POST', $url,
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
            ],
            json_encode($this->content['addBasket']));

        $response = $client->getResponse();

        $this->assertEquals(201 ,$response->getStatusCode());
        $this->assertRegExp(
            $this->expected['addBasket'],
            $response->getContent()
        );
    }
}