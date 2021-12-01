<?php

declare(strict_types=1);

namespace SandFox\Torrent;

final class MetaVersion
{
    public const V1 = 'v1';
    public const V2 = 'v2';
    // it will be an enum
    // phpcs:ignore Generic.NamingConventions.UpperCaseConstantName.ClassConstantNotUpperCase
    public const HybridV1V2 = 'v1v2';
}
