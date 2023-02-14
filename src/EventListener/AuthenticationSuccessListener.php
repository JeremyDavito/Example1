<?php

namespace App\EventListener;

use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;

class AuthenticationSuccessListener
{
    /**
     * @param AuthenticationSuccessEvent $event
     */
    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event)
    {
        $data = $event->getData();
        $user = $event->getUser();

        if (!$user instanceof User) {
            return;
        }

        $data['data'] = [
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'roles' => $user->getRoles(),
                'firstname' => $user->getFirstname(),
                'lastname' => $user->getLastname(),
                'phoneNumber' => $user->getPhoneNumber(),
                'avatar' => $user->getAvatar(),
                'address' => $user->getAddress(),
                'zipcode' => $user->getZipcode(),
                'city' => $user->getCity(),
                'country' => $user->getCountry(),
                'latitude' => $user->getLatitude(),
                'longitude' => $user->getLongitude(),                
            ]
        ];

        $event->setData($data);
    }
}
