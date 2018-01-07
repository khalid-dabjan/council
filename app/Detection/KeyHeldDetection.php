<?php
/**
 * Created by PhpStorm.
 * User: khaliddabjan
 * Date: 8/9/17
 * Time: 3:43 PM
 */

namespace App\Detection;


class KeyHeldDetection
{
    public function detect($body)
    {
        if (preg_match('/(.)\\1{4,}/', $body)) {
            throw new \Exception('your reply is spam');
        }
    }
}