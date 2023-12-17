Signing
#######

.. highlight:: php

.. versionadded:: 5.2

.. note:: BEP-35_ Torrent Signing
.. _BEP-35: https://www.bittorrent.org/beps/bep_0035.html

``sign()``
==========

::

    <?php

    $cert = openssl_x509_read('cert.pem');
    $key = openssl_pkey_get_private('pkey.pem');
    $torrent->sign(
        // private key
        key: $pkey,
        // certificate
        certificate: $cert,
        // include certificate into the file
        // if you are using update-url, set false here
        // because you already have the originator field with the same cert
        includeCertificate: true,
        // additional signed info
        // the spec does not define any fields here so its use is not recommended
        info = [],
    );

.. note:: A torrent file may have multiple signatures with different common names (cert CN fields)

``verifySignature()``
=====================

Verify the signature with a certificate::

    <?php

    $cert = openssl_x509_read('cert.pem'); // some known cert
    // or
    // for example if the torrent has originator field,
    // you can get the cert there
    $cert = $torrent->getOriginator();

    // Result is an instance of Arokettu\Torrent\DataTypes\SignatureValidatorResult
    $result = $torrent->verifySignature($cert);

Possible results:

* ``SignatureValidatorResult::Valid``: the signature is valid
* ``SignatureValidatorResult::Invalid``: the signature is not valid
* ``SignatureValidatorResult::NotPresent``: the torrent is not signed with a certificate with the given common name

.. note:: The method does not check validity of the certificate itself

``isSigned()``
==============

Check if a torrent file has signatures.

``getSignatures()``
===================

Get list of available signatures.

``removeSignatures()``
======================

Remove signatures.
This is required to modify info fields.
