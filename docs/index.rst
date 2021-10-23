Torrent File
############

|Packagist| |GitLab| |GitHub| |Bitbucket| |Gitea|

A PHP Class to work with torrent files

Installation
============

.. code-block:: bash

    composer require sandfoxme/torrent-file

Documentation
=============

.. toctree::
   :maxdepth: 2

   upgrade

Usage
=====

Load existing torrent file
--------------------------

.. code-block:: php

    <?php

    use SandFox\Torrent\TorrentFile;

    // from file
    $torrent = TorrentFile::load('debian.torrent');
    // from string
    $torrent = TorrentFile::loadFromString(file_get_contents('debian.torrent'));
    // from stream
    $torrent = TorrentFile::loadFromStream(fopen('debian.torrent', 'r'));

Create torrent file for existing directory or file
--------------------------------------------------

.. code-block:: php

    <?php

    use SandFox\Torrent\TorrentFile;

    $torrent = TorrentFile::fromPath('/home/user/ISO/Debian', [
        'pieceLength' => 512 * 1024,    // torrent chunk size (default: 512 KiB)
        'md5sum' => false,              // generate md5 sums for files (default: false)
        'sortFiles' => true,            // sort files in info dictionary by name (default: true)
    ]);

    // pass an instance of PSR-14 event dispatcher to receive progress events:
    $torrent = TorrentFile::fromPath('/home/user/ISO/Debian', [], $eventDispatcher);
    // dispatcher will receive instances of \SandFox\Torrent\FileSystem\FileDataProgressEvent
    //    only in 2.0 and later

Save torrent file
-----------------

.. code-block:: php

    <?php

    // to file
    $torrent->store('debian.torrent');

    // to string. for example, for downloading
    header('Content-Type: application/x-bittorrent');
    header('Content-Disposition: attachment; filename="' . urlencode($torrent->getFileName()) . '"');
    echo $torrent->storeToString();

    // to stream. useful for psr-7
    $response
        ->withHeader('Content-Type', 'application/x-bittorrent')
        ->withBody($streamFactory->createStreamFromResource($torrent->storeToStream()));

Basic fields manipulation
-------------------------

.. code-block:: php

    <?php

    // main announce url
    $announce = $torrent->getAnnounce();
    $torrent->setAnnounce('https://example.com/tracker');

    // additional announce urls
    $announces = $torrent->getAnnounceList();
    // plain ordered list
    $torrent->setAnnounceList([
        'https://example.net/tracker',
        'https://example.org/tracker',
    ]);
    // or with tier grouping
    $torrent->setAnnounceList([
        ['https://example.com/tracker', 'https://example.net/tracker'],
        'https://example.org/tracker',
    ]);

    // creation date
    $created = $torrent->getCreationDate();
    $torrent->setCreationDate(time());

    // comment
    $comment = $torrent->getComment();
    $torrent->setComment('This is a very cool torrent');

    // created by
    $createdBy = $torrent->getCreatedBy();
    $torrent->setCreatedBy('Me');

    // private marker
    $private = $torrent->isPrivate();
    $torrent->setPrivate(true);

Magnet Link
-----------

.. code-block:: php

    <?php
    // generate magnet link
    $torrent->getMagnetLink(); // 'magnet:?xt=urn:btih:...'

Possible future features
------------------------

- Files model (chunks and offsets for files)
- Chunks model (files and their offsets, chunk data validation)
- Info verification for existing files on disk

License
=======

The library is available as open source under the terms of the `MIT License`_.

.. _MIT License: https://opensource.org/licenses/MIT

.. |Packagist|  image:: https://img.shields.io/packagist/v/sandfoxme/torrent-file.svg?style=flat-square
   :target:     https://packagist.org/packages/sandfoxme/torrent-file
.. |GitHub|     image:: https://img.shields.io/badge/get%20on-GitHub-informational.svg?style=flat-square&logo=github
   :target:     https://github.com/arokettu/torrent-file
.. |GitLab|     image:: https://img.shields.io/badge/get%20on-GitLab-informational.svg?style=flat-square&logo=gitlab
   :target:     https://gitlab.com/sandfox/torrent-file
.. |Bitbucket|  image:: https://img.shields.io/badge/get%20on-Bitbucket-informational.svg?style=flat-square&logo=bitbucket
   :target:     https://bitbucket.org/sandfox/torrent-file
.. |Gitea|      image:: https://img.shields.io/badge/get%20on-Gitea-informational.svg?style=flat-square&logo=gitea
   :target:     https://sandfox.org/sandfox/torrent-file
