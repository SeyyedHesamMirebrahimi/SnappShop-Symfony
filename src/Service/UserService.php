<?php

namespace App\Service;

use App\Entity\User;
use App\Service\SMS\KaveNegar;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserService
{
    public UserPasswordHasherInterface $hasher;
    private ManagerRegistry $em;
    private TranslatorInterface $translator;

    public function __construct(UserPasswordHasherInterface $passwordHasher, ManagerRegistry $em, TranslatorInterface $translator)
    {
        $this->hasher = $passwordHasher;
        $this->em = $em;
        $this->translator = $translator;
    }

    public function register($mobile, $password, $name): array
    {
        try {
            $repo = $this->em->getRepository(User::class);
            if ($repo->findOneBy(['mobile' => $mobile])) {
                return [
                    'success' => false,
                    'message' => $this->translator->trans('mobileExist')
                ];
            }
            $user = new User();
            $user->setPassword($this->hasher->hashPassword($user, $password));
            $user->setMobile($mobile);
            $user->setName($name);
            if ($_ENV['APP_ENV'] == 'dev'){
                $user->setVerified(1);
            }else{
                $user->setVerified(0);
            }

            $code = random_int(11111, 99999);
            $user->setVerifyCode($code);
            $sms = new KaveNegar($mobile, str_ireplace('%code%',$code , $this->translator->trans('verifySms')) );
            $sms->send();
            $this->em->getManager()->persist($user);
            $this->em->getManager()->flush();
            return [
                'success' => true,
                'message' => $this->translator->trans('registerSuccessful')
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $this->translator->trans('publicError')
            ];
        }

    }

    public function verify($mobile, $code): array
    {
        try {
            $repo = $this->em->getRepository(User::class);
            $user = $repo->findOneBy(['mobile' => $mobile , 'verifyCode' => $code]);
            if ($user){
                $user->setVerified(1);
                $this->em->getManager()->flush();
                return [
                    'success' => true,
                    'message' => $this->translator->trans('verifySuccessful')
                ];
            }
            return [
                'success' => false,
                'message' => $this->translator->trans('wrongData')
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $this->translator->trans('publicError')
            ];
        }
    }

    public function login($mobile, $password):array
    {
        try {
            $repo = $this->em->getRepository(User::class);
            $user = $repo->findOneBy(['mobile' => $mobile]);
            if (!$user){
                return [
                    'success' => false,
                    'message' => $this->translator->trans('noMobile')
                ];
            }
            if ($this->hasher->isPasswordValid($user,$password)){
                return [
                    'success' => true,
                    'message' => $user->getToken()
                ];
            }else{
                return [
                    'success' => false,
                    'message' => $this->translator->trans('wrongPassword')
                ];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $this->translator->trans('publicError')
            ];
        }
    }


}