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

   saveload
   upgrade

Usage
=====

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
