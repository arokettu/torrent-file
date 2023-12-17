<?php

declare(strict_types=1);

namespace Arokettu\Torrent\TorrentFile;

use Arokettu\Bencode\Encoder;
use Arokettu\Torrent\DataTypes\Internal\DictObject;
use Arokettu\Torrent\DataTypes\Internal\InfoDict;
use Arokettu\Torrent\Exception\RuntimeException;
use Arokettu\Torrent\Helpers\CertHelper;
use OpenSSLAsymmetricKey;
use OpenSSLCertificate;

trait SignatureMethods
{
    abstract private function getField(string $key): mixed;
    abstract private function setField(string $key, mixed $value): void;
    abstract private function info(): InfoDict;

    abstract private static function encoder(): Encoder;

    public function isSigned(): bool
    {
        return $this->getField('signatures') !== null;
    }

    public function removeSignatures(): void
    {
        $this->setField('signatures', null);
    }

    public function sign(
        OpenSSLAsymmetricKey|OpenSSLCertificate $key,
        ?OpenSSLCertificate $certificate,
        bool $includeCertificate = false,
        ?DictObject $info = null,
    ): void {
        if (!openssl_x509_check_private_key($certificate, $key)) {
            throw new RuntimeException('The key does not correspond to the certificate');
        }

        $certData = openssl_x509_parse($certificate);
        $commonName = $certData['subject']['CN'] ??
            throw new RuntimeException('The certificate must contain a common name');

        $data = $this->info()->infoString;
        if ($info) {
            $data .= self::encoder()->encode($this->info());
        }

        openssl_sign($data, $signature, $key, OPENSSL_ALGO_SHA1) ?:
            throw new RuntimeException('Signing failed');

        if ($includeCertificate) {
            openssl_x509_export($certificate, $certPem);
            $certExport = CertHelper::extractPemCertificate($certPem);
        } else {
            $certExport = null;
        }

        $signatureHash = new DictObject([
            'signature' => $signature,
            'info' => $info,
            'certificate' => $certExport,
        ]);

        $signatures = $this->getField('signatures') ?? new DictObject([]);
        $signatures = $signatures->withOffset($commonName, $signatureHash);

        $this->setField('signatures', $signatures);
    }
}
