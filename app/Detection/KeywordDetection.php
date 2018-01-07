<?php
/**
 * Created by PhpStorm.
 * User: khaliddabjan
 * Date: 8/9/17
 * Time: 3:40 PM
 */

namespace App\Detection;


class KeywordDetection
{
    protected $keywords = [
        'yahoo customer support'
    ];

    public function detect($body)
    {
        foreach ($this->keywords as $keyWord) {
            if (stripos($body, $keyWord) !== false) {
                throw new \Exception('your reply is spam');
            }
        }
    }
}