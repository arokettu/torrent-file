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
.. versionadded:: 2.2 pieceAlign, detectExec, detectSymlinks
.. versionchanged:: 2.2 sortFiles, md5sum became noop
.. versionadded:: 2.3/3.1 version

The library can create a torrent file from scratch for a file or a directory.

.. code-block:: php

    <?php

    use SandFox\Torrent\TorrentFile;

    $torrent = TorrentFile::fromPath(
        '/home/user/ISO/Debian',
        pieceLength: 512 * 1024,
    );

    // pass an instance of PSR-14 event dispatcher to receive progress events:
    $torrent = TorrentFile::fromPath('/home/user/ISO/Debian', $eventDispatcher);
    // dispatcher will receive instances of \SandFox\Torrent\FileSystem\FileDataProgressEvent
    //    only in 2.0 and later

Available options:

``version``
    BitTorrent metadata file version.

    * ``MetaVersion::V1`` as described in BEP-3_ spec.
    * ``MetaVersion::V2`` as described in BEP-52_ spec.
    * ``MetaVersion::HybridV1V2`` with both V1 and V2 metadata.

    Default: ``MetaVersion::HybridV1V2`` (was ``MetaVersion::V1`` in 2.x)
``pieceLength``
    The number of bytes that each logical piece in the peer protocol refers to.
    Must be a power of 2 and at least 16 KiB.
    Default: ``524_288`` (512 KiB)
``pieceAlign``
    Align files to piece boundaries by inserting pad files.
    The option is ignored for V2 and V1+V2 torrent files because files in V2 are always aligned.

    * ``true``: Align all files
    * ``false``: Do not align
    * ``int $bytes``: Align files larger than ``$bytes`` in length

    Default: ``false``
``detectExec``
    The library detects executable attribute and sets it on files.
    Default: ``true``
``detectSymlinks``
    The library detects symlinks and creates symlink torrent objects.
    Only symlinks leading to files in the torrent data directory are detected.
    Default: ``false``

.. _BEP-3:  https://www.bittorrent.org/beps/bep_0003.html
.. _BEP-52: https://www.bittorrent.org/beps/bep_0052.html

.. note::
    Defaults may change in minor versions.
    If you care about their specific values, set them explicitly.

.. warning::
    Parameter order is not guaranteed for options.
    Please use named parameters.
