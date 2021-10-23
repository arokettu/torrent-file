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

A method to get infohash of the torrent file.

.. code-block:: php

    <?php
    $infoHash = $torrent->getInfoHash();

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

Raw Data
========

.. warning::
    Since 2.2 ``getRawData()`` is guaranteed to return the structure as it is encoded in the bencoded torrent file.
    In earlier versions it returned whatever junk TorrentFile stored internally.

.. versionchanged:: 2.2 Consistent return format

A helper method that dumps raw torrent data as array.

.. code-block:: php

    <?php
    // get raw data
    $data = $torrent->getRawData();
    // info dictionary
    var_dump($data['info']);
    // fields
    var_dump($data['creation date']);
    // etc...
