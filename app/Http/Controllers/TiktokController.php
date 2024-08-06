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
//        $data = $client->Product->uploadProductImage(public_path('img/454280251_2242598772767956_2031439075475016717_n.jpg'));
//        dd($data);

        $products = $client->Product->getCategories();
        dd($products);
    }


}
