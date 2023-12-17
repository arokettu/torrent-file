<?php

declare(strict_types=1);

namespace Arokettu\Torrent\Helpers;

use Arokettu\Torrent\Exception\RuntimeException;
use OpenSSLCertificate;

/**
 * @internal
 */
final class CertHelper
{
    private static function assertOpenSSL(): void
    {
        if (\extension_loaded('openssl') === false) {
            // @codeCoverageIgnoreStart
            // coverage is generated with openssl
            throw new RuntimeException('OpenSSL extension is not installed');
            // @codeCoverageIgnoreEnd
        }
    }

    public static function convertObjectToDer(OpenSSLCertificate $certificate): string
    {
        openssl_x509_export($certificate, $pem);
        return self::convertPemToDer($pem);
    }

    public static function convertPemToObject(string $pem): OpenSSLCertificate
    {
        self::assertOpenSSL();
        return openssl_x509_read($pem);
    }

    public static function convertDerToObject(string $der): OpenSSLCertificate
    {
        return self::convertPemToObject(self::convertDerToPem($der));
    }

    public static function convertPemToDer(string $pem): string
    {
        if (!preg_match('/-----BEGIN CERTIFICATE-----[\r\n]+(.*)[\r\n]+-----END CERTIFICATE-----/s', $pem, $matches)) {
            // @codeCoverageIgnoreStart
            throw new \LogicException('Cert not found'); // openssl bug?
            // @codeCoverageIgnoreEnd
        }

        return base64_decode($matches[1]);
    }

    public static function convertDerToPem(string $der): string
    {
        $encoded = base64_encode($der);
        $lines = str_split($encoded, 80);

        return "-----BEGIN CERTIFICATE-----\n" . implode("\n", $lines) . "\n-----END CERTIFICATE-----\n";
    }
}
