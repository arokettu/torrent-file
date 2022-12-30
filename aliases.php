<?php

declare(strict_types=1);

// This file is not autoloaded, but hopefully it would be indexed by the ide.
// This should make this namespace change on a minor version more IDE friendly.

// main classes
class_alias(Arokettu\Torrent\TorrentFile::class, SandFox\Torrent\TorrentFile::class);
class_alias(Arokettu\Torrent\MetaVersion::class, SandFox\Torrent\MetaVersion::class);

// exceptions
class_alias(Arokettu\Torrent\Exception\BadMethodCallException::class, SandFox\Torrent\Exception\BadMethodCallException::class);
class_alias(Arokettu\Torrent\Exception\InvalidArgumentException::class, SandFox\Torrent\Exception\InvalidArgumentException::class);
class_alias(Arokettu\Torrent\Exception\OutOfBoundsException::class, SandFox\Torrent\Exception\OutOfBoundsException::class);
class_alias(Arokettu\Torrent\Exception\PathNotFoundException::class, SandFox\Torrent\Exception\PathNotFoundException::class);
class_alias(Arokettu\Torrent\Exception\RuntimeException::class, SandFox\Torrent\Exception\RuntimeException::class);
class_alias(Arokettu\Torrent\Exception\TorrentFileException::class, SandFox\Torrent\Exception\TorrentFileException::class);

// data types
class_alias(Arokettu\Torrent\DataTypes\AnnounceList::class, SandFox\Torrent\DataTypes\AnnounceList::class);
class_alias(Arokettu\Torrent\DataTypes\Node::class, SandFox\Torrent\DataTypes\Node::class);
class_alias(Arokettu\Torrent\DataTypes\NodeList::class, SandFox\Torrent\DataTypes\NodeList::class);
class_alias(Arokettu\Torrent\DataTypes\UriList::class, SandFox\Torrent\DataTypes\UriList::class);
