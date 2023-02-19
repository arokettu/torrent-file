Info Fields
###########

Fields of the info dictionary of the torrent file.
The info dictionary is the primary data of the torrent file.
Using any setters here will change infoHash and the result will be considered a separate torrent file by the trackers.

Directory or File
=================

.. versionadded:: 2.2

A method to check if a directory or a file is encoded.

.. code-block:: php

    <?php
    $isDirectory = $torrent->isDirectory();

Info Hash
=========

Methods to get info hash of the torrent file.

Single hash
-----------

.. versionchanged:: 2.3/3.1
.. versionchanged:: 5.0 getInfoHashV1 and getInfoHashV2 replaced with the $version param of the getInfoHash()

Get V1 info hash if V1 metadata is present or null if not.

.. code-block:: php

    <?php
    $infoHash = $torrent->getInfoHash(MetaVersion::V1);

Get V2 info hash if V2 metadata is present or null if not.

.. code-block:: php

    <?php
    $infoHash = $torrent->getInfoHash(MetaVersion::V2);

.. versionchanged:: 2.3/3.1 The method returns V2 info hash if the metadata is present

Get V2 info hash if V2 metadata is present, fall back to V1 info hash.

.. code-block:: php

    <?php
    $infoHash = $torrent->getInfoHash();

Use binary representation instead of hex:

    <?php
    $infoHashBin = $torrent->getInfoHash(MetaVersion::V2, true);
    // or
    $infoHashBin = $torrent->getInfoHash(binary: true);

All hashes
----------

.. versionadded:: 2.3/3.1

Get all available hashes as array.

.. code-block:: php

    <?php
    $infoHashes = $torrent->getInfoHashes();
    $infoHashes[1]; // V1 info hash if V1 metadata is present
    $infoHashes[2]; // V2 info hash if V2 metadata is present

Name
====

A base name of the encoded file or directory.

.. warning::
    Setter will do a minimal check that the name can be a valid file name:
    it should not be empty and should not contain slashes and zero bytes.
    It also won't allow you to unset the name.

    However the content of the name field in the parsed file is not guaranteed to exist or be valid.

.. code-block:: php

    <?php
    // should be a valid file/dir name
    $torrent->setName('file.iso');
    // stored name may be null or invalid :(
    $name = $torrent->getName();

Private
=======

.. note:: BEP-27_ Private Torrents
.. _BEP-27: https://www.bittorrent.org/beps/bep_0027.html

Get / set / unset the private flag.

.. code-block:: php

    <?php
    $isPrivate = $torrent->isPrivate();
    $torrent->setPrivate(true);
