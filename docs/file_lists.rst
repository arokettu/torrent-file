.. _fileLists:

File Lists
##########

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
