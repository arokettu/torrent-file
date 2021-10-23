Fields
######

All fields can be unset by passing ``null``.

Announce
========

The URL of the tracker.
``string``.

.. code-block:: php

    <?php
    $torrent->setAnnounce('udp://example.com/announce');
    $announce = $torrent->getAnnounce();

Announce List
=============

.. note:: BEP-12_ Multitracker Metadata Extension
.. _BEP-12: https://www.bittorrent.org/beps/bep_0012.html

.. versionadded:: 2.2 ``AnnounceList`` object

A list of lists of tracker URLs.
See :ref:`the type section <type_AnnounceList>` for acceptable formats.

.. code-block:: php

    <?php
    // accepts AnnounceList objects or arrays of valid structure
    $torrent->setAnnounceList([['udp://example.com/announce']]);
    // get Announce List as array (in 3.0: as AnnounceList object)
    $torrent->getAnnounceList();
    // get Announce List as array
    $torrent->getAnnounceListAsArray();
    // get Announce List as AnnounceList object
    $torrent->getAnnounceListAsObject();

Comment
=======

Optional description.
``string``.

.. code-block:: php

    <?php
    $torrent->setComment('My Torrent');
    $comment = $torrent->getComment();

Created By
==========

Optional info about the creator.
``string``.

.. code-block:: php

    <?php
    $torrent->setCreatedBy('Me');
    $createdBy = $torrent->getCreatedBy();

Creation Date
=============

.. versionadded:: 2.2 ``DateTime`` based logic

Optional info about the creator.
``DateTimeImmutable`` / ``int``.

.. code-block:: php

    <?php
    // set by DateTime or DateTimeImmutable
    $torrent->setCreationDate(new DateTime('now'));
    // set by int timestamp
    $torrent->setCreationDate(time());

    // get int timestamp (in 3.0: DateTimeImmutable object)
    $creationDate = $torrent->getCreationDate();
    // get int timestamp
    $creationDate = $torrent->getCreationDateAsTimestamp();
    // get DateTimeImmutable object
    $creationDate = $torrent->getCreationDateAsDateTime();

Http Seeds
==========

.. note:: BEP-17_ HTTP Seeding
.. _BEP-17: https://www.bittorrent.org/beps/bep_0017.html

A list of HTTP seeding URLs.

Nodes
=====

.. note:: BEP-5_ DHT Protocol
.. _BEP-5: https://www.bittorrent.org/beps/bep_0005.html

A list of DHT nodes.

Url List
========

.. note:: BEP-19_ WebSeed - HTTP/FTP Seeding
.. _BEP-19: https://www.bittorrent.org/beps/bep_0019.html
