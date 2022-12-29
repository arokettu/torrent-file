# Changelog

[//]: <> (Contributor list:)
[@renan-s-oliveira]: https://github.com/renan-s-oliveira

## 1.x

### 1.4.0

*Dec 30, 2022*

* Alias all classes in `SandFox\Torrent\*` to `Arokettu\Torrent\*` in preparation for 4.0
* Old package `sandfoxme/torrent-file` is now provided by the new package

### 1.3.2

*Dec 14, 2022*

* sandfoxme/bencode -> arokettu/bencode

### 1.3.1

_Nov 9, 2020_

* Use saner url encoder for `getMagnetLink()`
* Bump PHP requirement from 7.1.0 to 7.1.3 to match dependencies

### 1.3.0

_Nov 9, 2020_

* Add `getDisplayName()`
* Add `getFileName()`
* Add `getMagnetLink()`
* Filter announce list from duplicates

### 1.2.0

_Oct 29, 2020_

* Add saving and loading torrent from bencoded string
* Deprecate direct use of TorrentFile constructor
* Clean up internal data structure to avoid potentially invalid torrent file
* Make library work in PHP strict mode
* Allow unsetting comment
* Fix Announce List behavior so it allows an arbitrary composition for groups

### 1.1.1

_Oct 29, 2020_

* Fix notice on `getComment()` if comment is not set [[gh#5]] by [@renan-s-oliveira]
* Fix `announce-list` not conforming to spec [[gh#4]] by [@renan-s-oliveira]

[gh#4]: https://github.com/arokettu/torrent-file/pull/4/
[gh#5]: https://github.com/arokettu/torrent-file/pull/5/

### 1.1.0

_Oct 6, 2020_

* Expose options array to `fromFile()` call
* Fix `getAnnounceList()` is broken after `setAnnounceList()` is used 

### 1.0.2

_Oct 6, 2020_

* Fix torrent creation
* Fix info hash calculation

### 1.0.1

_Jul 21, 2020_

* Allow bencode v2 and Symfony v5

### 1.0.0

_Apr 19, 2019_

* Force announce list to be List
* Remove encoding field
* Release as 1.0.0 because it is quite stable

## 0.x

### 0.1.1

_Nov 6, 2017_

Update Symfony dependencies to allow Symfony 4

### 0.1.0

_Mar 30, 2017_

Initial release
Basic work with torrent files
