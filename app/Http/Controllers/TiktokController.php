<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\refresh_token;
use EcomPHP\TiktokShop\Errors\AuthorizationException;
use Illuminate\Http\Request;
use EcomPHP\TiktokShop\Client;
use Carbon\Carbon;

class TiktokController extends Controller
{
    public function getToken(){

        $client_id = env('TIKTOK_CLIENT_ID');
        $service_id = env('TIKTOK_SERVICE_ID');
        $redirect_uri = urlencode('http://127.0.0.1:8000/callback');
        $scope = 'user_info';
        $state = 'test123456acbvccxvwd';

//        $authUrl = "https://services.tiktokshops.us/open/authorize?service_id=" . $service_id . "&response_type=code&scope=" . $scope . "&redirect_uri=" . $redirect_uri . "&state=" . $state;

        $authUrl = "https://auth.tiktok-shops.com/oauth/authorize?app_id=". $client_id . "&redirect_uri=" . $redirect_uri . "&state=" . $state . "&scope=" . $scope . "";
        echo '<a href="https://services.tiktokshops.us/open/authorize?service_id=7394581650464507691&redirect_uri=http://127.0.0.1:8000/callback&state=test123456acbvccxvwd&scope=user_info">Đăng nhập với TikTok Shop</a>';
//        $app_key = env('TIKTOK_CLIENT_ID');
//        $app_secret = env('TIKTOK_SECRET');
//
//        $client = new Client($app_key, $app_secret);
//        $auth = $client->auth();
//        $_SESSION['state'] = $state = '1231231asdasdasd'; // random string
//        $authUrl = $auth->createAuthRequest($state, true);
//
//// redirect user to auth url
//        header('Location: '.$authUrl);
//exit();
//        $authorization_code = $_GET['code'];
//        $token = $auth->getToken($authorization_code);
//
//        $access_token = $token['access_token'];
//        $refresh_token = $token['refresh_token'];
//        $access_token = $token['access_token'];
//        $client->setAccessToken($access_token);
//
//        $authorizedShopList = $client->Authorization->getAuthorizedShop();
    }

    public function getTokenAuth(){
        $app_key = env('TIKTOK_CLIENT_ID');
        $app_secret = env('TIKTOK_SECRET');
        $client = new Client($app_key, $app_secret);
        $auth = $client->auth();
        $_SESSION['state'] = $state = '1231231asdasdasd'; // random string
        $authUrl = $auth->createAuthRequest($state, true);

        $token = $auth->getToken('TTP_Oko0hwAAAAAFNpK0v4UmwyCKFwHowVCGU6-sQnRItMmbcU5Dt7hGa4X-E16Gdqf9ZNpi4BMtdlxGRDH3fLLZkMAPpeQn5sikYeuOIpChvlhqv4L55EHmK43HwjTxwhTH3ejfuaNLMXs-Jg2fQWojvlBr5XP4JOEG_pRkvX6lYHzPvGnUM4HLjQ');
        $data['refresh_code'] = $token['refresh_token'];
        $data['created_at'] = Carbon::now();
        $data['created_at'] = Carbon::now();

        refresh_token::create($data);
    }

    public function showShops(){
        $refresh_token_list = refresh_token::first();
        $refresh_token = $refresh_token_list->refresh_code;
        $app_key = env('TIKTOK_CLIENT_ID');
        $app_secret = env('TIKTOK_SECRET');

        $client = new Client($app_key, $app_secret);
        $auth = $client->auth();

        $new_token = $auth->refreshNewToken($refresh_token);

        $new_access_token = $new_token['access_token'];
        $new_refresh_token = $new_token['refresh_token'];
        $refresh_token_id = refresh_token::findOrFail(0);
        $refresh_token_id->refresh_code = $new_refresh_token;
        $refresh_token_id->save();
        $client->setAccessToken($new_access_token);
        $authorizedShopList = $client->Authorization->getAuthorizedShop();
        $client->setShopCipher($authorizedShopList['shops'][0]['cipher']);

        $timestamp = time();

        $params = array(
            'app_key' => $app_key,
            'shop_id' => $authorizedShopList['shops'][0]['id'],
            'timestamp' => $timestamp,
            'file' => 'public/img/454280251_2242598772767956_2031439075475016717_n.jpg'
        );

        $paramString = http_build_query($params);

        $signature = hash_hmac('sha256', $paramString, $app_secret);
        echo "<pre>";
        echo 'Signature: ' . $signature;
        print_r($authorizedShopList);
        print_r($new_access_token);
        $warehourse = $client->Logistic->getWarehouseList();
        $products = $client->Product->getCategories();
        $products = $products['categories'];
        $array = [];
        foreach ($products as $value){
            if ($value['local_name'] === 'Womenswear & Underwear'){
                $array[] = $value;
            }
            if ($value['local_name'] === 'T-shirts'){
                $array[] = $value;
            }

            if ($value['local_name'] === "Women's Tops"){
                $array[] = $value;
            }
        }

        $product = [
            "title" => "Áo Thun Nam Cotton 100% | Thương Hiệu: ABC | Mẫu: T123",
            "product_name" => "test",
            "description" => "test",
            "category_id" => "601302",
            "is_cod_open" => true,
            "package_dimensions" => [  // Kích thước gói hàng
                "height" => "10",     // Chiều cao, định dạng chuỗi
                "length" => "10",     // Chiều dài, định dạng chuỗi
                "width" => "10",      // Chiều rộng, định dạng chuỗi
                "unit" => "CENTIMETER" // Đơn vị kích thước, xác nhận giá trị hợp lệ với API
            ],
            "package_weight" => [
                "value" => "1.32",
                "unit" => "KILOGRAM",
            ],
            "package_dimension_unit" => "metric",

            "images" => [
                [
                    "id" => "tos-useast5-i-omjb5zjo8w-tx/d6d4893dbffa4a7f8ea358bb5ec07331"
                ]
            ],

            "main_images" => [
                [
                    "uri" => "tos-useast5-i-omjb5zjo8w-tx/e8d3ffa78bdf45eaa2cf35257660dc4a"
                ]
            ],
            "skus" => [
                [
                    "combined_skus" => [],
                    "external_urls" => [],
                    "price" => [
                        "amount" => "999", // Giá của SKU dưới dạng chuỗi
                        "currency" => "USD" // Đơn vị tiền tệ của giá
                    ],
                    "sales_attributes" => [],
                    "inventory" => [
                        [
                            "warehouse_id" => "7389568644710352682"
                        ]
                    ]
                ]
            ]
        ];

//        $products = $client->Product->createProduct($product);
//        $products = $client->Product->uploadProductImage('https://scontent.fhan4-4.fna.fbcdn.net/v/t39.30808-6/454280251_2242598772767956_2031439075475016717_n.jpg?_nc_cat=102&ccb=1-7&_nc_sid=6ee11a&_nc_ohc=fO96L36olqgQ7kNvgE_LKVz&_nc_ht=scontent.fhan4-4.fna&oh=00_AYAMmJup2MJJ6GcyuHQqzIjK3q2UvCYf1dsS8OFdbmFfLw&oe=66B7B007');

        //udate product
        $dataUpdate = [
            "title" => "Áo Thun Nam Cotton 100% | Thương Hiệu: ABC | Mẫu: T123",
            "description" => 'test',
            "category_id" => "601302",
            "main_images" => [
                [
                    "uri" => "tos-useast5-i-omjb5zjo8w-tx/e8d3ffa78bdf45eaa2cf35257660dc4a"
                ]
            ],
            "is_cod_open" => true,
            "package_dimensions" => [  // Kích thước gói hàng
                "height" => "10",     // Chiều cao, định dạng chuỗi
                "length" => "10",     // Chiều dài, định dạng chuỗi
                "width" => "10",      // Chiều rộng, định dạng chuỗi
                "unit" => "CENTIMETER" // Đơn vị kích thước, xác nhận giá trị hợp lệ với API
            ],
            "package_weight" => [
                "value" => "1.32",
                "unit" => "KILOGRAM",
            ],
            "package_dimension_unit" => "metric",

            "skus" => [
                [
                    "id" => "1729575785671593987",
                    "sales_attributes" => [
                        [
                            "attribute_id" => "100089",
                            "attribute_name" => "Color",
                            "value_id" => "1729592969712207000",
                            "value_name" => "Red"
                        ]
                    ],
                    "price" => [
                        "amount" => "999", // Giá của SKU dưới dạng chuỗi
                        "currency" => "USD" // Đơn vị tiền tệ của giá
                    ],
                    "inventory" => [
                        [
                            "quantity" => 100,
                            "warehouse_id" => "7389568644710352682"
                        ]
                    ]
                ]
            ]
        ];
        $products = $client->Product->editProduct('1729575737232494595', $dataUpdate);
dd($products);
    }


}
