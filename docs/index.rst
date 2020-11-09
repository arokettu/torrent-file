Torrent File
############

|Packagist| |GitLab| |GitHub| |Bitbucket| |Gitea|

A PHP Class to work with torrent files

Installation
============

.. code-block:: bash

    composer require sandfoxme/torrent-file

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

Create torrent file for existing directory or file
--------------------------------------------------

.. code-block:: php

    <?php

    use SandFox\Torrent\TorrentFile;

    $torrent = TorrentFile::fromPath('/home/user/ISO/Debian');

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

    // generate magnet link
    $torrent->getMagnetLink(); // 'magnet:?xn=urn:btih:...'

Possible future features
------------------------

- Files model (chunks and offsets for files)
- Chunks model (files and their offsets, chunk data validation)
- Info verification for existing files on disk

License
=======

The library is available as open source under the terms of the `MIT License`_.

.. _MIT License: https://opensource.org/licenses/MIT

.. |Packagist|  image:: https://img.shields.io/packagist/v/sandfoxme/torrent-file.svg
   :target:     https://packagist.org/packages/sandfoxme/torrent-file
.. |GitHub|     image:: https://img.shields.io/badge/get%20on-GitHub-informational.svg?logo=github
   :target:     https://github.com/arokettu/torrent-file
.. |GitLab|     image:: https://img.shields.io/badge/get%20on-GitLab-informational.svg?logo=gitlab
   :target:     https://gitlab.com/sandfox/torrent-file
.. |Bitbucket|  image:: https://img.shields.io/badge/get%20on-Bitbucket-informational.svg?logo=bitbucket
   :target:     https://bitbucket.org/sandfox/torrent-file
.. |Gitea|      image:: https://img.shields.io/badge/get%20on-Gitea-informational.svg?logo=gitea
   :target:     https://sandfox.org/sandfox/torrent-file
