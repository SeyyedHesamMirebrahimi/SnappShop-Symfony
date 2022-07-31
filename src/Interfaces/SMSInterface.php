<?php

namespace App\Interfaces;

interface SMSInterface
{
    public function __construct(string|array $mobile , $message);
    public function send();
}
