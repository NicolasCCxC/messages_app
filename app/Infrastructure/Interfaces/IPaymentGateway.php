<?php

namespace App\Infrastructure\Interfaces;

interface IPaymentGateway
{

    public function allowPaymentMethods() : array;

    /**
     * THIS FUNCTION GET ALL pse banks
     *
     * @return array
     */
    public function getPseBanks() : array;

    /**
     * This function allow to do a pse transfer
     *
     * @param array $data
     * @param string $companyId
     * @param string $userId
     * @return array
     */
    public function pseTransfer(array $data, string $companyId, string $userId) : array;

    /**
     * This function allow to do a credit card buy
     *
     * @param array $data
     * @param string $companyId
     * @param string $userId
     * @return array
     */
    public function creditCardTransfer(array $data, string $companyId, string $userId) : array;

    /**
     * This function allow to do a cash between buy
     *
     * @param array $data
     * @param string $companyId
     * @param string $userId
     * @return array
     */
    public function cashTransfer(array $data, string $companyId, string $userId) : array;

    /**
     * This function is to get transaction
     *
     * @param string $id
     * @param string $companyId
     * @param string $userId
     * @return array
     */
    public function report(string $id, string $companyId = null, string $userId = null) : array;
}
