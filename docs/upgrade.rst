Upgrade Notes
#############

Upgrade from 4.x to 5.0
=======================

* ``getInfoHashV1()`` and ``getInfoHashV2()`` were removed

    * Use ``getInfoHash()`` with $version: ``$torrent->getInfoHash(MetaVersion::V1)``

Upgrade from 3.x to 4.0
=======================

* Namespace was changed to ``Arokettu\\Torrent\\``

    * Alases for the new namespace were added to 1.4.0, 2.4.0, 3.2.0 for future compatibility
* ``getRawData()`` returns immutable objects with ArrayAccess&Countable&Iterable capabilities instead of arrays

    * Use ``getRawData()->getArray()`` to have a real array
* Getter methods on ``Node`` and ``FileDataEvent`` were removed, use exposed public readonly properties

Upgrade from 2.x to 3.0
=======================

* PHP 8.1 is now required.
* ``TorrentFile::forPath()`` uses named parameters instead of options array.
* | ``$torrent->getCreationDate()`` returns ``DateTimeImmutable``.
  | Use ``$torrent->getCreationDate()->getTimestamp()`` for int timestamp.
* | ``$torrent->getAnnounceList()`` now returns an instance of ``AnnounceList``.
  | Use ``$torrent->getAnnounceList()->toArray()`` for array.

Upgrade from 1.x to 2.0
=======================

Breaking changes:

* PHP 7.4 is now required.
* Custom event system based on ``FileDataProgress`` is removed. It was never documented anyway.
