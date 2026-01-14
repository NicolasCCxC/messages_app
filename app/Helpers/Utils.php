<?php

namespace App\Helpers;

use App\Exceptions\PayErrorException;
use App\Infrastructure\Gateway\MembershipPayment;

class Utils
{
    public static function createSignatureToPayU(
        string $apiKey,
        string $merchantId,
        string $invoice,
        string $txValue,
        string $currency
    )
    {
        $signature = $apiKey . '~' .
            $merchantId  . '~' .
            $invoice  . '~' .
            $txValue  . '~' .
            $currency;

        return md5($signature);
    }

    /**
     * @throws PayErrorException
     */
    public static function checkCreditCardType(string $number, string $paymentMethod)
    {
        try {
            $cardsPatterns = [
                'visa' => '(4\d{12}(?:\d{3})?)',
                'amex' => '(3[47]\d{13})',
                'mastercard' => '(5[1-5]\d{14})',
            ];

            $cardsName = [
                'VISA',
                'AMEX',
                'MASTERCARD'
            ];

            $matches = [];

            $pattern = "#^(?:".implode("|", $cardsPatterns).")$#";

            $result = preg_match($pattern, $number, $matches);

            return ($result>0) ? ($cardsName[count($matches)-2] === $paymentMethod) : false;
        }catch (Exception $e){
            throw new PayErrorException();
        }
    }

    public static function luhn_check(string $number): bool
    {
        // Set the string length and parity
        $number_length=strlen($number);
        $parity=$number_length % 2;

        // Loop through each digit and do the maths
        $total=0;
        for ($i=0; $i<$number_length; $i++) {
            $digit=$number[$i];
            // Multiply alternate digits by two
            if ($i % 2 === $parity) {
                $digit*=2;
                // If the sum is two digits, add them together (in effect)
                if ($digit > 9) {
                    $digit-=9;
                }
            }
            // Total up the digits
            $total+=$digit;
        }

        // If the total mod 10 equals 0, the number is valid
        return $total % 10 === 0;
    }

    public static function ping_payu(){
        $ping = new MembershipPayment();
        $responsePing = $ping->ping();

        if ($responsePing['code'] === 'SUCCESS' && $responsePing['error'] === null){
            return true;
        }

        return false;
    }
}
