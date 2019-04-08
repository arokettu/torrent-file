PHP Torrent File Library
========================

.. image::  https://img.shields.io/packagist/v/sandfoxme/torrent-file.svg?maxAge=2592000
   :target: https://packagist.org/packages/sandfoxme/torrent-file
   :alt:    Packagist
.. image::  https://img.shields.io/github/license/sandfoxme/torrent-file.svg?maxAge=2592000
   :target: https://opensource.org/licenses/MIT
   :alt:    Packagist
.. image::  https://img.shields.io/travis/sandfoxme/torrent-file.svg?maxAge=2592000
   :target: https://travis-ci.org/sandfoxme/torrent-file
   :alt:    Travis
.. image::  https://img.shields.io/codeclimate/c/sandfoxme/torrent-file.svg?maxAge=2592000
   :target: https://codeclimate.com/github/sandfoxme/torrent-file/coverage
   :alt:    Code Climate
.. image::  https://img.shields.io/codeclimate/maintainability/sandfoxme/torrent-file.svg?maxAge=2592000
   :target: https://codeclimate.com/github/sandfoxme/torrent-file
   :alt:    Code Climate

*Work in progress*

A PHP Class to work with torrent files

Done:
-----

Torrent loading from file
~~~~~~~~~~~~~~~~~~~~~~~~~

.. code-block:: php

    <?php

    use SandFox\Torrent\TorrentFile; 

    $torrent = TorrentFile::load('debian.torrent');

Creating torrent for existing directory or file
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

.. code-block:: php

    <?php

    use SandFox\Torrent\TorrentFile; 

    $torrent = TorrentFile::fromPath('/home/user/ISO/Debian');

Saving torrent file
~~~~~~~~~~~~~~~~~~~

.. code-block:: php

    <?php

    $torrent->store('debian.torrent');

Basic fields manipulation
~~~~~~~~~~~~~~~~~~~~~~~~~

.. code-block:: php

    <?php 

    $torrent->setAnnounce('https://example.com/tracker');

.. _todo-for-10:

TODO for 1.0:
-------------

-  Files model (chunks and offsets for files)
-  Chunks model (files and their offsets, chunk data validation)
-  Info verification for existing files on disk
-  Maximum number of related BEPs
-  Tests
-  Documentation

Installation
------------

Add this to your ``composer.json``:

.. code-block:: json

    {
        "require": {
            "sandfoxme/torrent-file": "^0.1.0"
        }
    }

License
-------

The library is available as open source under the terms of the `MIT License <https://opensource.org/licenses/MIT>`__.
