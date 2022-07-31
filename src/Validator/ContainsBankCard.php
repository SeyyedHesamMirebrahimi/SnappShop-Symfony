<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class ContainsBankCard extends Constraint
{
    public $message = 'شماره موبایل وارد شده صحیح نمی باشد';
    public $mode = 'strict'; // If the constraint has configuration options, define them as public properties
}