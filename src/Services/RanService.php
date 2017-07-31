<?php namespace Ran\Client\Services;

use Illuminate\Support\Facades\Log;
use Ran\Client\Contracts\RanServiceContract;
use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;


class RanService implements RanServiceContract
{

    private $client;


    /**
     * RanService constructor.
     *
     * Instantiating a guzzleHttp client using ENV configuration.
     *
     * RAN_BASE_URI: The full uri of the ran server
     * RAN_TOKEN_API: The token API provided by ran
     *
     */
    public function __construct()
    {
        $base_uri = env("RAN_BASE_URI");
        $token_api = env("RAN_TOKEN_API");
        $this->client = new Client([
            "base_uri" => $base_uri,
            'verify' => false,
            'http_errors' => false,
            'headers' => [
                "Authorization" => "Bearer " . $token_api,
                "Accept" => "application/json",
                "Content-Type" => "application/json"
            ]
        ]);
    }

    /**
     *
     * Create a user
     *
     * @param $email
     *
     * return a Ran user object
     */
    public function createUser($email)
    {
        $response = $this->client->request('POST', 'customers', [
            'json' => [
                'email' => $email
            ],
        ]);
        return $this->_decode($response);
    }

    /**
     * @param $productID
     *
     * @return Ran product Object
     */
    public function getProduct($productID)
    {
        $response = $this->client->request('GET', 'products/' . $productID);
        return $this->_decode($response);
    }

    /**
     * @param $customerID
     * @param $storeID
     *
     * @return cart Ran object
     */
    public function createCart($customerID, $storeID)
    {

        $data = ['store_id' => $storeID];
        if ($customerID) {
            $data['customer_id'] = $customerID;
        }

        $response = $this->client->request('POST', 'carts', [
            'json' => $data
        ]);
        return $this->_decode($response);
    }

    /**
     * @param $cartID
     * @return return an array of Ran cart items object
     */
    public function getCartItems($cartID)
    {
        $response = $this->client->request('GET', 'carts/' . $cartID . '/items');
        return $this->_decode($response);
    }

    /**
     * @param $cartID
     */
    public function destroyCart($cartID)
    {
        $this->client->request('DELETE', 'carts/' . $cartID);
    }

    /**
     * @param $cartID
     * @param $products
     * @return return an array of Ran cart items object
     */
    public function addItems($cartID, $products)
    {
        foreach ($products as $product) {
            $response = $this->client->request('POST', 'carts/' . $cartID . "/items", [
                'json' => [
                    'product_id' => $product['product_id'],
                    'quantity' => $product['quantity']
                ],
            ]);
            $result = $this->_decode($response);
        }
        $meta = $result['meta'];
        unset($result['meta']);
        $products = array(
            'list' => $result,
            'meta' => $meta
        );
        return $products;
    }

    /**
     * @param $cartID
     * @param $itemID
     * @param $productID
     */
    public function updateItem($cartID, $itemID, $productID)
    {
        $response = $this->client->request('PUT', 'carts/' . $cartID . "/items/" . $itemID, [
            'json' => [
                "product_id" => $productID,
                "quantity" => 1
            ]
        ]);
    }

    /**
     * @param $customerID
     * @param $products
     * @return a Ran cart object and array of cart items object
     */
    public function createCartAndItem($customerID, $products)
    {
        $cart = $this->createCart($customerID);
        $cartID = $cart['id'];
        $products = $this->addItems($cartID, $products);
        return [$cart, $products];
    }

    /**
     * @param $cartID
     * @param $data
     * @return mixed
     */
    public function checkout($cartID, $data)
    {
        $response = $this->client->request('POST', 'carts/' . $cartID . "/checkout", [
            'json' => $data
        ]);
        return $this->_decode($response);
    }

    /**
     * @param $cartID
     * @param $data
     * @return array|mixed
     */
    public function payment($cartID, $data)
    {
        try {
            $response = $this->client->request('POST', 'orders/' . $cartID . "/payment", [
                'json' => $data
            ]);
            return $this->_decode($response);
        } catch (ConnectException $e) {
            Log::Error("Error connecting to server: " . $e->getMessage());
            return [
                "errors" => [
                    ["message" => "Sorry, an error contacting the server occurred please try again later."]
                ]
            ];
        } catch (RequestException $e) {
            Log::Error("Error connecting to server: " . $e->getMessage());
            return [
                "errors" => [
                    ["message" => "Sorry, an error contacting the server occurred please try again later."]
                ]
            ];

        } catch (ServerException $e) {
            Log::Error("Error connecting to server: " . $e->getMessage());
            return [
                "errors" => [
                    ["message" => "Sorry, an error contacting the server occurred please try again later."]
                ]
            ];
        }
    }

    //
    // PRIVATE HELPER FUNCTIONS
    //

    /**
     * @param $response
     * @return mixed
     */
    private function _decode($response)
    {
        $decoded = \GuzzleHttp\json_decode($response->getBody(), true);
        if (isset($decoded['errors'])) {
            return $decoded;
        }
        $data = $decoded['data'];
        if (isset($decoded['meta'])) {
            $data['meta'] = $decoded['meta'];
        }
        return $data;
    }

}

?>
