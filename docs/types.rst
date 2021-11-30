Data Types
##########

All data types are immutable.
All lists implement ``Traversable``, ``Countable`` and a read only ``ArrayAccess`` with numeric keys.
All lists wipe duplicates.

.. _type_AnnounceList:

AnnounceList
============

``AnnounceList`` is a list of ``UriList``'s.
It represents the outer list in the ``announce-list`` field in the torrent file.

Creation
--------

Announce list can be created from the following structures:

.. code-block:: php

    <?php

    use SandFox\Torrent\DataTypes\AnnounceList;
    use SandFox\Torrent\DataTypes\UriList;

    // Build from iterable
    $announceList = AnnounceList::fromIterable([
        ['url1', 'url2'],   // a list of urls with the same priority
        'url3',             // a single url will be a list of a single item
    ]);
    // OOP way
    $announceList = AnnounceList::create(
        UriList::create('url1', 'url2'),
        UriList::create('url3'),
    );

Modification
------------

.. code-block:: php

    <?php

    use SandFox\Torrent\DataTypes\AnnounceList;
    use SandFox\Torrent\DataTypes\UriList;

    // append a list
    $announceList = AnnounceList::append(
        $announceList,
        UriList::create('url4'),
    );
    // prepend a list
    $announceList = AnnounceList::prepend(
        $announceList,
        UriList::create('url4'),
    );
    // remove a list
    $announceList = AnnounceList::remove(
        $announceList,
        UriList::create('url4'),
    );

Array representation
--------------------

.. code-block:: php

    <?php

    // toArray() return an array of arrays of strings,
    // the same structure as it is represented in the torrent file
    $data = $announceList->toArray();
    // toArrayOfUriLists() return array of UriList objects
    $lists = $announceList->toArrayOfUriLists();

Node
====

``Node`` represents an item in the DHT nodes list.

Creation
--------

.. code-block:: php

    <?php

    use SandFox\Torrent\DataTypes\Node;

    // Build from array
    $node = Node::fromArray(['localhost', 11111]);
    // OOP way
    $node = new Node('127.0.0.1', 12345);

Fields
------

.. code-block:: php

    <?php

    $host = $node->host; // node host or ip
    $port = $node->port; // node port

    // also with array access that mimics the representation in the torrent file
    $host = $node[0];
    $port = $node[1];

Array representation
--------------------

.. code-block:: php

    <?php

    // toArray() return a node-array [$host, $port],
    // the same structure as it is represented in the torrent file
    $data = $node->toArray();

.. _type_NodeList:

NodeList
========

``NodeList`` is a list of ``Node``'s.
It represents the ``nodes`` field in the torrent file.

Creation
--------

Node list can be created from the following structures:

.. code-block:: php

    <?php

    use SandFox\Torrent\DataTypes\Node;
    use SandFox\Torrent\DataTypes\NodeList;

    // Build from iterable
    $nodeList = NodeList::fromIterable([
        ['localhost', 11111],   // [host|ip : string, port : int]
        ['127.0.0.1', 12345],   // [host|ip : string, port : int]
    ]);
    // OOP way
    $nodeList = NodeList::create(
        new Node('localhost', 11111),
        new Node('127.0.0.1', 12345),
    );

Modification
------------

.. code-block:: php

    <?php

    use SandFox\Torrent\DataTypes\Node;
    use SandFox\Torrent\DataTypes\NodeList;

    // append a node
    $nodeList = NodeList::append(
        $nodeList,
        new Node('fe00::1234', 12321),
    );
    // prepend a node
    $nodeList = NodeList::prepend(
        $nodeList,
        new Node('fe00::1234', 12321),
    );
    // remove a node
    $nodeList = NodeList::remove(
        $nodeList,
        new Node('fe00::1234', 12321),
    );

Array representation
--------------------

.. code-block:: php

    <?php

    // toArray() return an array of node-arrays [$host, $port],
    // the same structure as it is represented in the torrent file
    $data = $nodeList->toArray();
    // toArrayOfNodes() return array of Node objects
    $nodes = $nodeList->toArrayOfNodes();

.. _type_UriList:

UriList
=======

``UriList`` is a list of strings.
It represents the ``url-list`` and ``httpseeds`` fields
and the inner lists in the ``announce-list`` field in the torrent file.

Creation
--------

Uri list can be created from the following structures:

.. code-block:: php

    <?php

    use SandFox\Torrent\DataTypes\UriList;

    // Build from iterable
    $uriList = UriList::fromIterable([
        'https://example.com/announce',
        'udp://example.com/announce',
    ]);
    // OOP way
    $uriList = UriList::create(
        'https://example.com/announce',
        'udp://example.com/announce',
    );

Modification
------------

.. code-block:: php

    <?php

    use SandFox\Torrent\DataTypes\UriList;

    // append a list
    $uriList = UriList::append(
        $uriList,
        'udp://example.net/announce',
    );
    // prepend a list
    $uriList = UriList::prepend(
        $uriList,
        'udp://example.net/announce',
    );
    // remove a list
    $uriList = UriList::remove(
        $uriList,
        'udp://example.net/announce',
    );

Array representation
--------------------

.. code-block:: php

    <?php

    // toArray() return an array of strings,
    // the same structure as it is represented in the torrent file
    $data = $uriList->toArray();
