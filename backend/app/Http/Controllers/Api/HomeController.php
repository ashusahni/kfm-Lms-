<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Web\HomeController as WebHomeController;

class HomeController extends Controller
{
    /**
     * Return home page data for React/SPA.
     */
    public function index()
    {
        $webHome = new WebHomeController();
        $data = $webHome->getHomeData();
        return response()->json(['data' => $data]);
    }
}
