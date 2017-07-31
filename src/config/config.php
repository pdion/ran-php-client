<?php

/*
 *
 * base_uri : The url of the Ran server
 * token_api: The authentication token
 * store_id: The uuid of the store
 * product_id_list: An array containing the products.
 *      standard: contain the non-subscription products,
 *      subscription: contain the subscription products.
 *
 *      first uuid of the array is the cheapest/non-combo uuid product
 *      second uuid is the most expensive/combo uuid product
 *
 */

return [
    'base_uri'  => 'https://ran.strikewood.com',
    'token_api' => 'WkG2TVLf6bwzDwJkFeCYbfhuifeN11wFOhNZR1WXkuSt5vyqQqI1qxDPDUX0GEce',
    'store_id'  => '8db846d5-70b9-49ec-923e-aa38acc28d5d',
    'product_id_list' => [
        'standard'     => ['5788be08-6e73-47a8-b237-6938e467bc7b', '56196f34-fef3-4f2f-bbbf-7b7956040898'],
        'subscription' => ['c0eb34fd-0c7e-4e5b-a4a6-e3629bc19e20', 'eebeb3f5-e1fe-4c07-a05b-874df3c28924']
    ]
];

?>