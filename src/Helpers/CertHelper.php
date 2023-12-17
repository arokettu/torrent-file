<?php

declare(strict_types=1);

namespace Arokettu\Torrent\Helpers;

use Arokettu\Torrent\Exception\RuntimeException;
use OpenSSLCertificate;

final class CertHelper
{
    public static function assertOpenSSL(): void
    {
        if (\extension_loaded('openssl') === false) {
            throw new RuntimeException('OpenSSL extension is not installed');
        }
    }

    public static function storeObjectToDer(OpenSSLCertificate $certificate): string
    {
        openssl_x509_export($certificate, $pem);
        return self::extractDerFromPem($pem);
    }

    public static function readPemToObject(string $pem): OpenSSLCertificate
    {
        self::assertOpenSSL();
        return openssl_x509_read($pem);
    }

    public static function readDerToObject(string $der): OpenSSLCertificate
    {
        return self::readPemToObject(self::buildPemFromDer($der));
    }

    public static function extractDerFromPem(string $pem): string
    {
        if (!preg_match('/-----BEGIN CERTIFICATE-----[\r\n]+(.*)[\r\n]+-----END CERTIFICATE-----/s', $pem, $matches)) {
            throw new \LogicException('Cert not found'); // openssl bug?
        }

        return base64_decode($matches[1]);
    }

    public static function buildPemFromDer(string $der): string
    {
        $encoded = base64_encode($der);
        $lines = str_split($encoded, 80);

        return "-----BEGIN CERTIFICATE-----\n" . implode("\n", $lines) . "\n-----END CERTIFICATE-----\n";
    }
}
