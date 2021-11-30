Upgrade Notes
#############

Upgrade from 2.x to 3.0
=======================

* PHP 8.1 is now required.
* ``TorrentFile::forPath()`` uses named parameters instead of options array.
* | ``$torrent->getCreationDate()`` returns ``DateTimeImmutable``.
  | Use ``$torrent->getCreationDate()->getTimestamp()`` for int timestamp.
* | ``$torrent->getAnnounceList()`` now returns an instance of ``AnnounceList``.
  | Use ``$torrent->getAnnounceList()->toArray()`` for array.
* | ``FileDataProgressEvent`` now uses readonly properties instead of getters.
  | ``$event->getTotal()``    => ``$event->total``
  | ``$event->getDone()``     => ``$event->done``
  | ``$event->getFileName()`` => ``$event->fileName``

Upgrade from 1.x to 2.0
=======================

Breaking changes:

* PHP 7.4 is now required.
* Custom event system based on ``FileDataProgress`` is removed. It was never documented anyway.
