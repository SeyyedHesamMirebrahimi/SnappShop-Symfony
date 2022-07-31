<?php

namespace App\Request;

use Symfony\Component\Validator\Constraints as Assert;

final class VerifyRequest
{
    #[Assert\NotBlank]
    #[Assert\Regex(pattern: '/09(1[0-9]|3[1-9]|2[1-9])-?[0-9]{3}-?[0-9]{4}/')]
    public string $mobile;
    #[Assert\NotBlank]
    public string $code;
}