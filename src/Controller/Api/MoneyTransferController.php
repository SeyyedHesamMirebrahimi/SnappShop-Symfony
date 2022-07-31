<?php

namespace App\Controller\Api;

use App\Request\LoginRequest;
use App\Request\RegisterRequest;
use App\Request\TransferRequest;
use App\Service\TransferService;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class MoneyTransferController extends AbstractController
{
    private SerializerInterface $serializer;
    private ValidatorInterface $validation;
    private TransferService $service;
    private Request $request;

    public function __construct(SerializerInterface $serializer, ValidatorInterface $validator, TransferService $transferService, RequestStack $requestStack, TranslatorInterface $translator)
    {
        $this->serializer = $serializer;
        $this->validation = $validator;
        $this->service = $transferService;
        $this->request = $requestStack->getCurrentRequest();
    }

    #[Route('/api/money-transfer', name: 'app_api_money_transfer')]
    public function index(): Response
    {
        $data = $this->serializer->deserialize(
            json_encode($this->request->request->all()), TransferRequest::class, 'json');
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
        return $this->json($this->service->transfer($data->from, $data->to, $data->price));
    }

    #[Route('/api/most-recent', name: 'app_api_most_recent')]
    public function app_api_most_recent(): Response
    {
        return $this->json($this->service->mostRecent(10 , 3));
    }
}
