<?php

namespace App\Service;

use App\Entity\CardNumber;
use App\Entity\Fee;
use App\Entity\Transaction;
use App\Service\SMS\KaveNegar;
use Doctrine\Persistence\ManagerRegistry;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class TransferService
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

    #[ArrayShape(['success' => "false", 'message' => "string"])] public function transfer($from, $to, $price)
    {
        try {
            $cardRepo = $this->em->getRepository(CardNumber::class);
            $fromCard = $cardRepo->findOneBy(['cardtNumber' => $from]);
            if (!$fromCard){
                return [
                    'success' => false,
                    'message' => $this->translator->trans('invalidCardNumber')
                ];
            }
            $toCard = $cardRepo->findOneBy(['cardtNumber' => $to]);
            $fee = $this->em->getRepository(Fee::class)->findOneBy(['type' => 'transfer']) ? $this->em->getRepository(Fee::class)->findOneBy(['type' => 'transfer'])->getPrice() : 0;
            if ($fromCard->getAccountNumber()->getBalance() >= $price + $fee) {
//           Transaction for sender account to decrease price
                $lowTransaction = new Transaction();
                $lowTransaction->setPrice($price);
                $lowTransaction->setType(0);
                $lowTransaction->setCardNumber($fromCard);
                $lowTransaction->setDescription('انتثال وجه');
//            sms for decrease account
                $this->sendMessageByType(0, $fromCard);
                $this->em->getManager()->persist($lowTransaction);
//            transaction for bank fee
                $feeTransaction = new Transaction();
                $feeTransaction->setDescription('کسر کارمزد انتقال وجه');
                $feeTransaction->setCardNumber($fromCard);
                $feeTransaction->setType(0);
                $feeTransaction->setPrice($fee);
                $this->em->getManager()->persist($feeTransaction);
                $fromCard->getAccountNumber()->setBalance($fromCard->getAccountNumber()->getBalance() - ($price + $fee));
//            if the receptor card number is on our bank .....
                if ($toCard) {
                    $increaseTransaction = new Transaction();
                    $increaseTransaction->setPrice($price);
                    $increaseTransaction->setType(1);
                    $increaseTransaction->setCardNumber($toCard);
                    $increaseTransaction->setDescription('دریافت وجه');
                    $this->em->getManager()->persist($increaseTransaction);
                    $toCard->getAccountNumber()->setBalance($toCard->getAccountNumber()->getBalance() + $price);
                    $this->sendMessageByType(1, $toCard);
                }
                $this->em->getManager()->flush();
                return [
                    'success' => true,
                    'message' => $this->translator->trans('transactionSuccessful')
                ];
            } else {
                return [
                    'success' => false,
                    'message' => $this->translator->trans('lowBalance')
                ];
            }
        } catch (\Exception $exception) {
            return [
                'success' => false,
                'message' => $this->translator->trans('publicError')
            ];
        }
    }

    public function sendMessageByType(bool $type, CardNumber $cardNumber): bool
    {
        try {
            if ($type) {
                $sms = new KaveNegar($cardNumber->getAccountNumber()->getUser()->getMobile(), $this->translator->trans('decreaseBalanceSms'));
            } else {
                $sms = new KaveNegar($cardNumber->getAccountNumber()->getUser()->getMobile(), $this->translator->trans('increaseBalanceSms'));
            }
            $sms->send();
            return true;
        } catch (\Exception $exception) {
            return false;
        }
    }

    public function mostRecent($minutes, $transactionCountToShow): array
    {
        try {
        $array = [];
        $minutes = 100;
            $transactions = $this->em->getRepository(Transaction::class)->getMostRecent($minutes );
        foreach ($transactions as $transaction) {

            $userTransactions = $this->em->getRepository(Transaction::class)->getUserTransactions($transaction['userId'] ,$transactionCountToShow );
            $array[] = [
                'userId'  => $transaction['userId'],
                'total' => $transaction['total'],
                'transactions' => $userTransactions
            ];
        }
            return [
                'success' => true,
                'message' => $array
            ];
        }catch (\Exception $exception){
            return [
                'success' => false,
                'message' => $this->translator->trans('publicError')
            ];
        }
    }
}