<?php


namespace App\Services;


use App\Entity\URL;
use App\Entity\User;

class UrlService
{
    /**
     * @param array $requestData
     * @param User $user
     * @return URL
     */
    public function getNewURL(array $requestData, User $user): URL
    {
        return (new URL)
            ->setUser($user)
            ->setBase($requestData['base'])
            ->setShort($requestData['short'])
            ->setCreatedAt(new \DateTime);
    }
}