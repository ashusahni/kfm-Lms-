<?php

use App\Api\Response;
use App\Api\Request;

function validateParam($request_input, $rules, $somethingElseIsInvalid = null)
{
    $request = new Request();
    return $request->validateParam($request_input, $rules, $somethingElseIsInvalid);
}

function apiResponse2($success, $status, $msg, $data = null,$title=null)
{
    $response = new Response();
    return $response->apiResponse2($success, $status, $msg, $data,$title);
}


function apiAuth()
{
    if (request()->input('test_auth_id')) {
        return App\Models\Api\User::find(request()->input('test_auth_id')) ?? die('test_auth_id not found');
    }
    return auth('api')->user();


}

function nicePrice($price)
{
    if ($price === null || $price === '' || !is_numeric($price)) {
        return 0;
    }
    $value = handlePrice($price, false, false);
    return round(is_numeric($value) ? (float) $value : 0, 2);
}

function nicePriceWithTax($price)
{
    if ($price === null || $price === '' || !is_numeric($price)) {
        return ['price' => 0, 'tax' => 0];
    }
   // return round(handlePrice($price, true,false,true), 2);
    return handlePrice($price, false, false, true);
}




