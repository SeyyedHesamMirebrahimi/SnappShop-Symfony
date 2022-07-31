<?php

namespace App\Request;
use App\Validator as CustomAssert;
use Symfony\Component\Validator\Constraints as Assert;

final class TransferRequest
{
    #[Assert\NotBlank]
    #[CustomAssert\ContainsBankCard]
    public string $from;
    #[Assert\NotBlank]
    #[CustomAssert\ContainsBankCard]
    public string $to;
    #[Assert\NotBlank]
    #[Assert\GreaterThanOrEqual(value: 1000)]
    #[Assert\LessThanOrEqual(value: 50000000)]
    public string $price;


}