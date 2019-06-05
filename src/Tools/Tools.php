<?php
// src/Tools/Tools.php

namespace App\Tools;

use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\AbstractFOSRestController;

// Extending AbstractFOSRestController to be able to use
// AbstractFOSRestController::view() protected method.
class Tools extends AbstractFOSRestController
{
    public function setCache($fosRestObject, $maxAge, $objForView)
    {
        $response = new Response();

        // Cache for 3600 seconds
        $response->setSharedMaxAge($maxAge);

        // Set a custom Cache-Control directive
        $response->headers->addCacheControlDirective('must-revalidate', true);

        $view = $fosRestObject->view($objForView);
        $view->setResponse($response);

        return $fosRestObject->handleView($view);
    }

    public function getRandAlphaNumStr($len = 10)
    {
        $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charsLen = strlen($chars);
        $randomString = '';
        for ($i = 0; $i < $len; $i++) {
            $randomString .= $chars[rand(0, $charsLen - 1)];
        }
        return $randomString;
    }

    public function getRandAlphaNumStrLow($len = 10)
    {
        $chars = '0123456789abcdefghijklmnopqrstuvwxyz';
        $charsLen = strlen($chars);
        $randomString = '';
        for ($i = 0; $i < $len; $i++) {
            $randomString .= $chars[rand(0, $charsLen - 1)];
        }
        return $randomString;
    }

    public function getRandNumStr($len = 10)
    {
        $chars = '0123456789';
        $charsLen = strlen($chars);
        $randomNumString = '';
        for ($i = 0; $i < $len; $i++) {
            $randomNumString .= $chars[rand(0, $charsLen - 1)];
        }
        return $randomNumString;
    }

    public function getRandValFromArray($array)
    {
        $key = array_rand($array);
        $value = $array[$key];
        return $value;
    }
}
