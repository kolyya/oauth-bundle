<?php

namespace Kolyya\OAuthBundle\Enum;

class ServiceIdEnum
{
    const VK = 'vkontakte';
    const FB = 'facebook';
    const OK = 'odnoklassniki';
    const MR = 'mailru';
    const GG = 'google';

    public static $IDS = [
        self::VK     => 'vk',
        self::FB     => 'fb',
        self::OK     => 'ok',
        self::MR     => 'mr',
        self::GG     => 'gg',
    ];
}