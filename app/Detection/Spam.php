<?php
/**
 * Created by PhpStorm.
 * User: khaliddabjan
 * Date: 8/8/17
 * Time: 3:39 PM
 */

namespace App\Detection;


class Spam
{
    protected $detections = [
        KeywordDetection::class,
        KeyHeldDetection::class
    ];

    public function detect($body)
    {
        foreach ($this->detections as $detection) {
            app($detection)->detect($body);
        }
        return false;
    }
}