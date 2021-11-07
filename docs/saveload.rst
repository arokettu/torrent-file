Loading, Saving and Creating
############################

Load an existing torrent
========================

.. versionadded:: 1.2 loadFromString()
.. versionadded:: 2.1 loadFromStream()

You can load a torrent from file, from string, or from stream.

.. code-block:: php

    <?php

    use SandFox\Torrent\TorrentFile;

    // from file
    $torrent = TorrentFile::load('debian.torrent');
    // from string
    $torrent = TorrentFile::loadFromString(file_get_contents('debian.torrent'));
    // from stream
    $torrent = TorrentFile::loadFromStream(fopen('debian.torrent', 'r'));

Save torrent
============

.. versionadded:: 1.2 storeToString()
.. versionadded:: 2.1 storeToStream()

You can save your torrent to file, to string, or to stream.

.. code-block:: php

    <?php

    // to file
    $torrent->store('debian.torrent');
    // to string
    $torrentString = $torrent->storeToString();
    // to stream
    $torrent->storeToStream($stream);
    // to new php://temp stream
    $phpTemp = $torrent->storeToStream();

Create a torrent for existing directory or file
===============================================

.. versionadded:: 1.1 $options
.. versionadded:: 2.0 $eventDispatcher

The library can create a torrent file from scratch for a file or a directory.

.. code-block:: php

    <?php

    use SandFox\Torrent\TorrentFile;

    $torrent = TorrentFile::fromPath('/home/user/ISO/Debian', [
        'pieceLength' => 512 * 1024,
    ]);

    // pass an instance of PSR-14 event dispatcher to receive progress events:
    $torrent = TorrentFile::fromPath('/home/user/ISO/Debian', [], $eventDispatcher);
    // dispatcher will receive instances of \SandFox\Torrent\FileSystem\FileDataProgressEvent
    //    only in 2.0 and later

Available options:

``pieceLength``
    The number of bytes that each logical piece in the peer protocol refers to.
    Must be a power of 2 and at least 16 KiB.
    Default: ``524_288`` (512 KiB)
``pieceAlign``
    Align files to piece boundaries by inserting pad files.

    * ``true``: Align all files
    * ``false``: Do not align
    * ``int $bytes``: Align files larger than ``$bytes`` in length

    Default:
    ``false`` for V1 torrents,
    (in future versions)
    ignored, always assumed ``true`` for hybrid V1+V2 torrents,
    ignored, meaningless for V2 torrents
``detectExec``
    The library detects executable attribute and sets it on files.
    Default: ``true``
``detectSymlinks``
    The library detects symlinks and creates symlink torrent objects.
    Only symlinks leading to files in the torrent data directory are detected.
    Default: ``false``

.. note::
    Defaults may change in minor versions.
    If you care about their specific values, set them explicitly.
