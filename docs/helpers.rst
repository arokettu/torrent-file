Helper Methods
##############

Display Name
============

A helper to suggest a display name:

* name if name is set and is not empty
* infohash as a fallback

.. code-block:: php

    <?php
    $displayName = TorrentFile::fromPath('~/isos/debian.iso')
        ->getDisplayName(); // 'debian.iso'

File Name
=========

A helper to suggest a file name: ``getDisplayName() + '.torrent'``

.. code-block:: php

    <?php
    $filename = TorrentFile::fromPath('~/isos/debian.iso')
        ->getFileName(); // 'debian.iso.torrent'

Magnet Link
===========

A helper to generate a magnet link for the torrent.

.. code-block:: php

    <?php
    $magnet = $torrent->getMagnetLink(); // 'magnet:?xt=urn:btih:...'
