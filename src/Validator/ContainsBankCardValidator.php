<?php
// src/Validator/ContainsAlphanumericValidator.php
namespace App\Validator;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Symfony\Contracts\Translation\TranslatorInterface;

class ContainsBankCardValidator extends ConstraintValidator
{

    private TranslatorInterface $translator;

    public function __construct( TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function toEnglish($string): array|string
    {
        $persian = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        $arabic = ['٩', '٨', '٧', '٦', '٥', '٤', '٣', '٢', '١','٠'];
        $num = range(0, 9);
        $convertedPersianNums = str_replace($persian, $num, $string);
        return str_replace($arabic, $num, $convertedPersianNums);
    }

    public function validate($value, Constraint $constraint)
    {

        $card = (string) preg_replace('/\D/','',$this->toEnglish($value));
        $strlen = strlen($card);
        if($strlen!=16)
            return false;
        if(($strlen<13 or $strlen>19))
            return false;
        if(!in_array($card[0],[2,4,5,6,9]))
            return false;

        for($i=0; $i<$strlen; $i++)
        {
            $res[$i] = $card[$i];
            if(($strlen%2)==($i%2))
            {
                $res[$i] *= 2;
                if($res[$i]>9)
                    $res[$i] -= 9;
            }
        }
        if (!array_sum($res)%10==0){
            $this->context->buildViolation($this->translator->trans('invalidCardNumber'))
                ->addViolation();
        }
    }
}