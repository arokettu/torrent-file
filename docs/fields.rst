Root Fields
###########

Fields of the torrent file that are not parts of the info dictionary.
By changing these fields you're not creating a separate torrent file (not changing infohash).
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

A list of lists of tracker URLs.
See :ref:`the type section <type_AnnounceList>` for acceptable formats.

.. code-block:: php

    <?php
    // accepts AnnounceList objects or iterables of valid structure
    //      (same as AnnounceList::fromIterable())
    $torrent->setAnnounceList([['udp://example.com/announce']]);
    // get Announce List as AnnounceList object
    $torrent->getAnnounceList();
    // get Announce List as array
    $torrent->getAnnounceList()->toArray();

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

Optional info about the creation date.
``DateTimeImmutable``.

.. code-block:: php

    <?php
    // set by DateTime or DateTimeImmutable
    $torrent->setCreationDate(new DateTime('now'));
    // set by int timestamp
    $torrent->setCreationDate(time());
    // get DateTimeImmutable object
    $creationDate = $torrent->getCreationDate();
    // get int timestamp
    $creationDate = $torrent->getCreationDate()->getTimestamp();

Http Seeds
==========

.. note:: BEP-17_ HTTP Seeding
.. _BEP-17: https://www.bittorrent.org/beps/bep_0017.html

A list of HTTP seeding URLs.
See :ref:`the type section <type_UriList>` for acceptable formats.

.. code-block:: php

    <?php
    // accepts UriList objects or iterables of valid structure
    //      (same as UriList::fromIterable())
    $torrent->setHttpSeeds(['udp://example.com/seed']);
    // get Http Seeds as UriList object
    $torrent->getHttpSeeds();
    // get Http Seeds as array
    $torrent->getHttpSeeds()->toArray();

Nodes
=====

.. note:: BEP-5_ DHT Protocol
.. _BEP-5: https://www.bittorrent.org/beps/bep_0005.html

A list of DHT nodes.
See :ref:`the type section <type_NodeList>` for acceptable formats.

.. code-block:: php

    <?php
    // accepts NodeList objects or iterables of valid structure
    //      (same as NodeList::fromIterable())
    $torrent->setNodes(['udp://example.com/seed']);
    // get Url List as UriList object
    $torrent->getNodes();
    // get Url List as array
    $torrent->getNodes()->toArray();


Url List
========

.. note:: BEP-19_ WebSeed - HTTP/FTP Seeding
.. _BEP-19: https://www.bittorrent.org/beps/bep_0019.html

A list of webseed URLs.
See :ref:`the type section <type_UriList>` for acceptable formats.

.. code-block:: php

    <?php
    // accepts UriList objects or iterables of valid structure
    //      (same as UriList::fromIterable())
    $torrent->setUrlList(['udp://example.com/seed']);
    // get Url List as UriList object
    $torrent->setUrlList();
    // get Url List as array
    $torrent->setUrlList()->toArray();
