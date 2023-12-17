<?php

declare(strict_types=1);

namespace Arokettu\Torrent\TorrentFile;

use Arokettu\Bencode\Encoder;
use Arokettu\Torrent\DataTypes\Internal\DictObject;
use Arokettu\Torrent\DataTypes\Internal\InfoDict;
use Arokettu\Torrent\DataTypes\Signature;
use Arokettu\Torrent\DataTypes\SignatureValidatorResult;
use Arokettu\Torrent\Exception\RuntimeException;
use Arokettu\Torrent\Helpers\CertHelper;
use OpenSSLAsymmetricKey;
use OpenSSLCertificate;

use function iter\map;

/**
 * @internal
 */
trait SignatureMethods
{
    abstract private function getField(string $key): mixed;
    abstract private function setField(string $key, mixed $value): void;
    abstract private function info(): InfoDict;

    abstract private static function encoder(): Encoder;

    private ?DictObject $signatures = null;

    public function isSigned(): bool
    {
        return $this->getField('signatures') !== null;
    }

    /**
     * @return DictObject<Signature>
     */
    public function getSignatures(): DictObject
    {
        return $this->signatures ??
            new DictObject(map(fn ($s) => Signature::fromInternal($s), $this->getField('signatures') ?? []));
    }

    public function removeSignatures(): void
    {
        $this->setField('signatures', null);
        $this->signatures = new DictObject([]);
    }

    public function sign(
        OpenSSLAsymmetricKey|OpenSSLCertificate $key,
        OpenSSLCertificate $certificate,
        bool $includeCertificate = true,
        iterable $info = [],
    ): void {
        if (!openssl_x509_check_private_key($certificate, $key)) {
            throw new RuntimeException('The key does not correspond to the certificate');
        }

        $certData = openssl_x509_parse($certificate);
        $commonName = $certData['subject']['CN'] ??
            throw new RuntimeException('The certificate must contain a common name');

        $signInfo = new DictObject($info);

        $data = $this->info()->infoString;
        if ($info) {
            $data .= self::encoder()->encode($signInfo);
        }

        openssl_sign($data, $signature, $key, OPENSSL_ALGO_SHA1) ?:
            throw new RuntimeException('Signing failed');

        if ($includeCertificate) {
            $certExport = CertHelper::convertObjectToDer($certificate);
        } else {
            $certExport = null;
        }

        $signatureHash = new Signature($signature, $certExport, $signInfo);

        $signatures = $this->getSignatures();
        $signatures = $signatures->withOffset($commonName, $signatureHash);

        $this->signatures = $signatures;
        $this->setField('signatures', $signatures);
    }

    public function verifySignature(OpenSSLCertificate $certificate): SignatureValidatorResult
    {
        $certData = openssl_x509_parse($certificate);
        $commonName = $certData['subject']['CN'] ??
            throw new RuntimeException('The certificate must contain a common name');

        $signatures = $this->getSignatures();
        /** @var Signature $signature */
        $signature = $signatures[$commonName];
        if ($signature === null) {
            return SignatureValidatorResult::NotPresent; // not signed with this cert
        }

        $data = $this->info()->infoString;
        if ($signature->info->empty() === false) {
            $data .= self::encoder()->encode($signature->info);
        }

        return match (openssl_verify($data, $signature->signature, $certificate, OPENSSL_ALGO_SHA1)) {
            1 => SignatureValidatorResult::Valid,
            0 => SignatureValidatorResult::Invalid,
            -1 => throw new RuntimeException('Signature verification error: ' . openssl_error_string()),
        };
    }
}
