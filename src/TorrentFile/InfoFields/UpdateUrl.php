<?php

declare(strict_types=1);

namespace Arokettu\Torrent\TorrentFile\InfoFields;

use Arokettu\Torrent\Helpers\CertHelper;
use OpenSSLCertificate;

/**
 * @internal
 */
trait UpdateUrl
{
    abstract private function getInfoField(string $key): mixed;
    abstract private function setInfoField(string $key, mixed $value): void;

    public function setUpdateUrl(string $url, OpenSSLCertificate $certificate): void
    {
        $this->setInfoField('update-url', $url);
        $this->setInfoField('originator', CertHelper::convertObjectToDer($certificate));
    }

    public function removeUpdateUrl(): void
    {
        $this->setInfoField('update-url', null);
        $this->setInfoField('originator', null);
    }

    public function getUpdateUrl(): ?string
    {
        return $this->getInfoField('update-url');
    }

    public function getOriginator(): ?OpenSSLCertificate
    {
        $originator = $this->getInfoField('originator');
        return $originator ? CertHelper::convertDerToObject($originator) : null;
    }
}
