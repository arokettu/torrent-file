# PHP Torrent File Library

*Work in progress*

A PHP Class to work with torrent files

## Done:

### Torrent loading from file

```php
<?php

use SandFoxMe\Torrent\TorrentFile; 

$torrent = TorrentFile::load('debian.torrent');
```

### Creating torrent for existing directory or file

```php
<?php

use SandFoxMe\Torrent\TorrentFile; 

$torrent = TorrentFile::fromPath('/home/user/ISO/Debian');
```

### Saving torrent file

```php
<?php

$torrent->store('debian.torrent');
```

### Basic fields manipulation

```php
<?php 

$torrent->setAnnounce('https://example.com/tracker');
```

## TODO for 1.0:

* Files model (chunks and offsets for files)
* Chunks model (files and their offsets, chunk data validation)
* Info verification for existing files on disk
* Maximum number of related BEPs
* Tests
* Documentation

## Installation

Add this to your `composer.json`:

```json
{
    "require": {
        "sandfoxme/torrent-file": "^0.1.0"
    }
}
```

## License

The library is available as open source under the terms of the [MIT License](https://opensource.org/licenses/MIT).
