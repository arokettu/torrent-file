# Changelog

[//]: <> (Contributor list:)
[@renan-s-oliveira]: https://github.com/renan-s-oliveira

## 2.1.1

*Feb 25, 2021*

* Fixed deprecated behavior not removed in 2.0.0:
  `TorrentFile::__construct()` is now private

## 2.1.0

*Feb 19, 2021*

* Add Stream API from Bencode 1.5/2.5
* Add big integer support from Bencode 1.6/2.6

## 2.0.0

*Nov 17, 2020*

* Bump PHP requirement to PHP 7.4
* Replace `FileDataProgress` with Event Dispatcher ([PSR-14]) + `FileDataProgressEvent`
* 100% test coverage
 
[PSR-14]: https://www.php-fig.org/psr/psr-14/

## 1.3.1

*Nov 9, 2020*

* Use saner url encoder for `getMagnetLink()`
* Bump PHP requirement from 7.1.0 to 7.1.3 to match dependencies

## 1.3.0

*Nov 9, 2020*

* Add `getDisplayName()`
* Add `getFileName()`
* Add `getMagnetLink()`
* Filter announce list from duplicates

## 1.2.0

*Oct 29, 2020*

* Add saving and loading torrent from bencoded string
* Deprecate direct use of TorrentFile constructor
* Clean up internal data structure to avoid potentially invalid torrent file
* Make library work in PHP strict mode
* Allow unsetting comment
* Fix Announce List behavior so it allows an arbitrary composition for groups

## 1.1.1

*Oct 29, 2020*

* Fix notice on `getComment()` if comment is not set [[gh#5]] by [@renan-s-oliveira]
* Fix `announce-list` not conforming to spec [[gh#4]] by [@renan-s-oliveira]

[gh#4]: https://github.com/arokettu/torrent-file/pull/4/
[gh#5]: https://github.com/arokettu/torrent-file/pull/5/

## 1.1.0

*Oct 6, 2020*

* Expose options array to `fromFile()` call
* Fix `getAnnounceList()` is broken after `setAnnounceList()` is used

## 1.0.2

*Oct 6, 2020*

* Fix torrent creation
* Fix info hash calculation

## 1.0.1

*Jul 21, 2020*

* Allow bencode v2 and Symfony v5

## 1.0.0

*Apr 19, 2019*

* Force announce list to be List
* Remove encoding field
* Release as 1.0.0 because it is quite stable

## 0.1.1

*Nov 6, 2017*

Update Symfony dependencies to allow Symfony 4

## 0.1.0

*Mar 30, 2017*

Initial release
Basic work with torrent files
