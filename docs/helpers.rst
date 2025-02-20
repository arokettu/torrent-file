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

Raw Data
========

.. warning::
    Since 2.2 ``getRawData()`` is guaranteed to return the structure as it is encoded in the bencoded torrent file.
    In earlier versions it returned whatever junk TorrentFile stored internally.

A helper method that dumps raw torrent data as an array-like structure.

.. code-block:: php

    <?php
    // get raw data (in readonly array-like structures)
    $data = $torrent->getRawData();
    // info dictionary
    var_dump($data['info']);
    // fields
    var_dump($data['creation date']);
    // etc...

    // get a real array
    $array = $data->getArray();
