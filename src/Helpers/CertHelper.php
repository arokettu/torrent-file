<?php

declare(strict_types=1);

namespace Arokettu\Torrent\Helpers;

final class CertHelper
{
    public static function extractPemCertificate(string $pem): string
    {
        if (!preg_match('/-----BEGIN CERTIFICATE-----[\r\n]+(.*)[\r\n]+-----END CERTIFICATE-----/s', $pem, $matches)) {
            throw new \LogicException('Cert not found'); // openssl bug?
        }

        return base64_decode($matches[1]);
    }
}
