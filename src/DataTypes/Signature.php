<?php

declare(strict_types=1);

namespace Arokettu\Torrent\DataTypes;

use Arokettu\Bencode\Types\BencodeSerializable;
use Arokettu\Bencode\Types\DictType;
use Arokettu\Torrent\DataTypes\Internal\DictObject;
use Arokettu\Torrent\Helpers\CertHelper;
use OpenSSLCertificate;

final class Signature implements BencodeSerializable
{
    public readonly OpenSSLCertificate|null $certificate;
    public readonly string|null $certificatePem;
    public readonly DictObject $info;

    public function __construct(
        public readonly string $signature,
        public readonly string|null $certificateDer,
        DictObject|null $info,
    ) {
        unset($this->certificate);
        unset($this->certificatePem);

        $this->info = $info ?? new DictObject([]);
    }

    public static function fromInternal(DictObject $signature): self
    {
        return new self(
            $signature['signature'],
            $signature['certificate'],
            $signature['info'],
        );
    }

    public function __get(string $name): mixed
    {
        return match ($name) {
            'certificate' => $this->certificatePem ?
                CertHelper::convertPemToObject($this->certificatePem) :
                null,
            'certificatePem' => $this->certificatePem = $this->certificateDer ?
                CertHelper::convertDerToPem($this->certificateDer) :
                null,
        };
    }

    public function bencodeSerialize(): DictType
    {
        return new DictType([
            'signature' => $this->signature,
            'certificate' => $this->certificateDer,
            'info' => $this->info->empty() ? null : $this->info,
        ]);
    }
}
