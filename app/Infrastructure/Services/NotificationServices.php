<?php

namespace App\Infrastructure\Services;

use App\Enums\Services;
use App\Traits\CommunicationBetweenServicesTrait;
class NotificationServices
{

    use CommunicationBetweenServicesTrait;


    /**
     * Create notifications
     *
     * @param array $data
     * @param string $userId
     * @param string $companyId
     *
     * @services NOTIFICATION /notifications
     *
     * @return mixed
     */
    public function storeNotifications(array $data, string $userId, string $companyId)
    {
        return collect(
            $this->makeRequest(
                'POST',
                Services::NOTIFICATION,
                '/notifications',
                $userId,
                $companyId,
                $data
            )
        );
    }

}
