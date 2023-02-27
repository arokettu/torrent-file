.. _torrent_versions:

Versioned Info Fields
#####################

.. versionadded:: 5.0

Torrent File Version 1
======================

As described in BEP-3_.

.. _BEP-3: https://www.bittorrent.org/beps/bep_0003.html

Info Hash
---------

A method to get info hashes of the torrent file.

A method to get v1 info hash of the torrent file.

.. code-block:: php

    <?php
    $infoHash = $torrent->v1()->getInfoHash(); // hex
    $infoHash = $torrent->v1()->getInfoHash(true); // binary

Directory or File
-----------------

A method to check if a directory or a file is encoded.

.. code-block:: php

    <?php
    $isDirectory = $torrent->v1()->isDirectory();

File List
---------

Files class is a flat list of files, ordered by their order in the pieces hash.

.. code-block:: php

    <?php
    $files = $torrent->v1()->getFiles();

Files is iterable (IteratorAggregate):

.. code-block:: php

    <?php
    foreach ($files as $file) {
        // ...
    }

    // if you need to see hidden pad files for whatever reason:
    foreach ($files->getIterator(true) as $file) {
        // ...
    }

File object is a simple value object:

.. code-block:: php

    <?php
    $file->name; // file base name
    $file->path; // full path as array
    $file->length; // file size
    $file->attributes; // Attributes object
    $file->sha1; // sha1 sum in hex
    $file->sha1bin; // sha1 sum in binary
    $file->symlinkPath; // symlink path if $file->attibutes->symlink

For attributes object see :ref:`types section <type_Attributes>`.

Torrent File Version 2
======================

As described in BEP-52_.

.. _BEP-52: https://www.bittorrent.org/beps/bep_0052.html

Info Hash
---------

A method to get v2 info hash of the torrent file.

.. code-block:: php

    <?php
    $infoHash = $torrent->v2()->getInfoHash(); // hex
    $infoHash = $torrent->v2()->getInfoHash(true); // binary

Directory or File
-----------------

A method to check if a directory or a file is encoded.

.. code-block:: php

    <?php
    $isDirectory = $torrent->v2()->isDirectory();

File Tree
---------

File Tree class is a tree of files implementing RecursiveIterator.

.. code-block:: php

    <?php
    $fileTree = $torrent->v2()->getFileTree();

Iterate over files:

.. code-block:: php

    <?php
    $i = new \RecursiveIteratorIterator($fileTree);
    foreach ($i as $file) {
        // ...
    }

File object is a simple value object:

.. code-block:: php

    <?php
    $file->name; // file base name
    $file->path; // full path as array
    $file->length; // file size
    $file->attributes; // Attributes object
    $file->piecesRoot; // merkle tree pieces root in hex
    $file->piecesRootBin; // merkle tree pieces root in binary
    $file->symlinkPath; // symlink path if $file->attibutes->symlink

For attributes object see :ref:`types section <type_Attributes>`.
