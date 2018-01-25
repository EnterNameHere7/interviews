<?php

namespace Realmdigital\Web\Controller;

use DDesrosiers\SilexAnnotations\Annotations as SLX;
use Silex\Application;

/**
 * @SLX\Controller(prefix="product/")
 */
class ProductController {

    /**
     * @SLX\Route(
     *      @SLX\Request(method="GET", uri="/{id}")
     * )
     * @param Application $app
     * @param $name
     * @return
     */

    public function getById_GET(Application $app, $id){
        //Initialize array and set data to request by
        $requestData = array();
        $requestData['id'] = $id;

        $result[] = $this->getInfo($requestData);
        return $app->render('products/product.detail.twig', $result);
    }

    function  getInfo($requestData){
        //Initialize curl
        $curl = curl_init();

        //Set curl options and execute
        curl_setopt($curl, CURLOPT_URL,  'http://192.168.0.241/eanlist?type=Web');
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $requestData);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);

        //Decode Json response
        $response = json_decode($response);
        //Close connection
        curl_close($curl);
        $result = array();

        //Loop through the response
        for ($i = 0; $i <= count($response); $i++) {

            //Initialize array and load values into it
            $prod = array();
            $prod['ean'] = $response[$i]['barcode'];
            $prod["name"]= $response[$i]['itemName'];
            $prod["prices"] = array();

            //Loop through all the prices
            for ($j=0 ; $j <= count($response[$i]['prices']); $j++) {

                //Check if the currency is not ZAR
                if ($response[$i]['prices'][$j]['currencyCode'] != 'ZAR') {
                    $p_price = array();
                    $p_price['price'] = $response[$i]['prices'][$j]['sellingPrice'];
                    $p_price['currency'] = $response[$i]['prices'][$j]['currencyCode'];
                    $prod["prices"][] = $p_price;
                }
            }
            //Push array values into result array
            $result[] = $prod;
        }
        //Return result
        return $result;
    }

    /**
     * @SLX\Route(
     *      @SLX\Request(method="GET", uri="/search/{name}")
     * )
     * @param Application $app
     * @param $name
     * @return
     */
    public function getByName_GET(Application $app, $name){
        //Initialize array and set data to request by
        $requestData = array();
        $requestData['names'] = $name;

        $result[] = $this->getInfo($requestData);
        return $app->render('products/products.twig', $result);
    }

}
