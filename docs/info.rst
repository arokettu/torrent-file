Info Fields
###########

.. versionchanged:: 5.0 A lot of stuff was moved to version specific namespaces, see :ref:`fileLists`

Fields of the info dictionary of the torrent file.
The info dictionary is the primary data of the torrent file.
Using any setters here will change infoHash and the result will be considered a separate torrent file by the trackers.

Info Hash
=========

.. versionchanged:: 5.0 Specific info hashes were moved into version specific namespaces

A method to get info hashes of the torrent file.

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
