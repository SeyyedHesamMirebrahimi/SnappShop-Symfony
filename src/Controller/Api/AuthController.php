<?php

namespace App\Controller\Api;

use App\Request\LoginRequest;
use App\Request\RegisterRequest;
use App\Request\VerifyRequest;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;


class AuthController extends AbstractController
{
    private SerializerInterface $serializer;
    private ValidatorInterface $validation;
    private UserService $userService;
    private Request $request;

    public function __construct(SerializerInterface $serializer, ValidatorInterface $validator, UserService $userService, RequestStack $requestStack, TranslatorInterface $translator)
    {
        $this->serializer = $serializer;
        $this->validation = $validator;
        $this->userService = $userService;
        $this->request = $requestStack->getCurrentRequest();
    }

    #[Route('/api/register', name: 'app_api_register')]
    public function index(): JsonResponse
    {
        $data = $this->serializer->deserialize(
            json_encode($this->request->request->all()), RegisterRequest::class, 'json');
        $errorsArray = [];
        /**
         * @var ConstraintViolationList $errors
         */
        $errors = $this->validation->validate($data);
        for ($x=0 ; $x<= ($errors->count() - 1);$x++){
            $errorObject = $errors->get($x);
            $errorsArray[]  = [
                'field' => $errorObject->getPropertyPath(),
                'message' => $errorObject->getMessage()
            ];
        }
        if ($errors->count() > 0) {
            return $this->json([
                'success' => false,
                'message' => $errorsArray
            ]);
        }
        return $this->json($this->userService->register($data->mobile, $data->password, $data->name));
    }

    #[Route('/api/verify', name: 'app_api_verify')]
    public function verify(): JsonResponse
    {
        $data = $this->serializer->deserialize(
            json_encode($this->request->request->all()), VerifyRequest::class, 'json');
        $errorsArray = [];
        /**
         * @var ConstraintViolationList $errors
         */
        $errors = $this->validation->validate($data);
        for ($x=0 ; $x<= ($errors->count() - 1);$x++){
            $errorObject = $errors->get($x);
            $errorsArray[]  = [
                'field' => $errorObject->getPropertyPath(),
                'message' => $errorObject->getMessage()
            ];
        }
        if ($errors->count() > 0) {
            return $this->json([
                'success' => false,
                'message' => $errorsArray
            ]);
        }
        return $this->json($this->userService->verify($data->mobile, $data->code));
    }

    #[Route('/api/login', name: 'app_api_login')]
    public function login(): JsonResponse
    {
        $data = $this->serializer->deserialize(
            json_encode($this->request->request->all()), LoginRequest::class, 'json');
        $errorsArray = [];
        /**
         * @var ConstraintViolationList $errors
         */
        $errors = $this->validation->validate($data);
        for ($x=0 ; $x<= ($errors->count() - 1);$x++){
           $errorObject = $errors->get($x);
           $errorsArray[]  = [
               'field' => $errorObject->getPropertyPath(),
               'message' => $errorObject->getMessage()
           ];
        }
        if ($errors->count() > 0) {
            return $this->json([
                'success' => false,
                'message' => $errorsArray
            ]);
        }
        return $this->json($this->userService->login($data->mobile, $data->password));
    }
}
