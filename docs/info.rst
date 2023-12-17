Info Fields
###########

.. highlight:: php

.. versionchanged:: 5.0 A lot of stuff was moved to version specific namespaces, see :ref:`torrent_versions`
.. versionchanged:: 5.2 Setters will throw RuntimeException if the torrent is signed

Fields of the info dictionary of the torrent file.
The info dictionary is the primary data of the torrent file.
Using any setters here will change infoHash and the result will be considered a separate torrent file by the trackers.
Changing info fields of a signed torrent is forbidden.

Info Hash
=========

.. versionchanged:: 5.0 Specific info hashes were moved into version specific namespaces

A method to get info hashes of the torrent file.

All hashes
----------

.. versionadded:: 2.3/3.1

Get all available hashes as array.

::

    <?php
    $infoHashes = $torrent->getInfoHashes();
    $infoHashes[1]; // V1 info hash if V1 metadata is present
    $infoHashes[2]; // V2 info hash if V2 metadata is present

Metadata
========

Checks the version of the torrent.

::

    <?php
    $torrent->hasMetadata(MetaVersion::V1); // simple check, does not create v1 Files object
    // or
    $torrent->v1() !== null; // if you plan to iterate over files too

    $torrent->hasMetadata(MetaVersion::V2); // simple check, does not create v2 FileTree object
    // or
    $torrent->v2() !== null; // if you plan to iterate over files too

Metadata Removal
================

.. versionadded:: 5.1

Methods to "unhybridize" hybrid V1+V2 torrents.

Remove a specific version::

    <?php

    $torrent->removeMetadata(MetaVersion::V1);

or keep a specific version::

    <?php

    $torrent->keepOnlyMetadata(MetaVersion::V1);

Name
====

A base name of the encoded file or directory.

.. warning::
    Setter will do a minimal check that the name can be a valid file name:
    it should not be empty and should not contain slashes and zero bytes.
    It also won't allow you to unset the name.

    However the content of the name field in the parsed file is not guaranteed to exist or be valid.

::

    <?php
    // should be a valid file/dir name
    $torrent->setName('file.iso');
    // stored name may be null or invalid :(
    $name = $torrent->getName();

Private
=======

.. note:: BEP-27_ Private Torrents
.. _BEP-27: https://www.bittorrent.org/beps/bep_0027.html

Get / set / unset the private flag.

::

    <?php
    $isPrivate = $torrent->isPrivate();
    $torrent->setPrivate(true);

Update Url
==========

.. note:: BEP-39_ Updating Torrents Via Feed URL
.. _BEP-39: https://www.bittorrent.org/beps/bep_0039.html

Set / get / unset the update URL and the verification certificate.

::

    <?php

    $cert = openssl_x509_read('file://cert.pem');
    $torrent->setUpdateUrl('http://example.com/update', $cert);

    $torrent->getUpdateUrl(); // getter for the url
    $torrent->getOriginator(); // x.509 cert to verify infohash

    $torrent->removeUpdateUrl(); // separate unsetter because it's 2 fields

.. note:: To use this feature you must also sign your torrent with the same certificate
