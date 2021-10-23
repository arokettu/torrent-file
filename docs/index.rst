Torrent File
############

|Packagist| |GitLab| |GitHub| |Bitbucket| |Gitea|

A PHP Class to work with torrent files

Installation
============

.. code-block:: bash

   composer require sandfoxme/torrent-file

Documentation
=============

.. toctree::
   :maxdepth: 2

   saveload
   fields
   types
   upgrade
   plans

Usage
=====

Basic fields manipulation
-------------------------

.. code-block:: php

    <?php

    // private marker
    $private = $torrent->isPrivate();
    $torrent->setPrivate(true);

Magnet Link
-----------

.. code-block:: php

    <?php
    // generate magnet link
    $torrent->getMagnetLink(); // 'magnet:?xt=urn:btih:...'

License
=======

The library is available as open source under the terms of the `MIT License`_.

.. _MIT License: https://opensource.org/licenses/MIT

.. |Packagist|  image:: https://img.shields.io/packagist/v/sandfoxme/torrent-file.svg?style=flat-square
   :target:     https://packagist.org/packages/sandfoxme/torrent-file
.. |GitHub|     image:: https://img.shields.io/badge/get%20on-GitHub-informational.svg?style=flat-square&logo=github
   :target:     https://github.com/arokettu/torrent-file
.. |GitLab|     image:: https://img.shields.io/badge/get%20on-GitLab-informational.svg?style=flat-square&logo=gitlab
   :target:     https://gitlab.com/sandfox/torrent-file
.. |Bitbucket|  image:: https://img.shields.io/badge/get%20on-Bitbucket-informational.svg?style=flat-square&logo=bitbucket
   :target:     https://bitbucket.org/sandfox/torrent-file
.. |Gitea|      image:: https://img.shields.io/badge/get%20on-Gitea-informational.svg?style=flat-square&logo=gitea
   :target:     https://sandfox.org/sandfox/torrent-file
