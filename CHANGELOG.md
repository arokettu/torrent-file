# Changelog

[//]: <> (Contributor list:)
[@renan-s-oliveira]: https://github.com/renan-s-oliveira

## 3.0.0

*Nov 30, 2021*

(forked from 2.2.0)

* PHP 8.1 is now required
* In `TorrentFile::forPath()` options array is removed and replaced with named parameters
* `getCreationDate()` now returns an instance of `DateTimeImmutable`
  * `getCreationDateAsDateTime()` and `getCreationDateAsTimestamp()` now trigger silent deprecations
* `getAnnounceList()` now returns an instance of `AnnounceList`
  * `getAnnounceListAsArray()` and `getAnnounceListAsObject()` now trigger silent deprecations
* `FileDataProgressEvent`: getters were replaced with readonly properties

## 2.2.0

*Nov 30, 2021*

* Fixed possible announce list corruption if deduplication of trackers happened
* Fixed `null` handling on `announce` and `created by` 
* `getRawData()` now always returns the representation as it would appear in the saved file
* `TorrentFile` now serializes in cross version compatible manner
* Added 2 temporary methods: `getCreationDateAsDateTime()` and `getCreationDateAsTimestamp()`
  to simplify future migration to 3.x.
  In 3.x `getCreationDate()` will return `DateTimeImmutable` instead of `int`
* Added 2 temporary methods: `getAnnounceListAsArray()` and `getAnnounceListAsObject()`
  to simplify future migration to 3.x.
  In 3.x `getAnnounceList()` will return `AnnounceList` instead of `string[][]`
* Added support for `nodes` field (BEP-5)
* Added support for `httpseeds` field (BEP-17)
* Added support for `url-list` field (BEP-19)
* Added `setName()` and `getName()`
* Added `isDirectory()`
* `sortFiles` option is deprecated and no longer has any effect. Files are always sorted now (BEP-52 compatibility)
* `pieceLength` option is validated to be pow of 2 and at least 16 KiB (BEP-52 compatibility)
* `md5sum` option is deprecated and no longer has any effect
* sha1 sums for all files are now generated (BEP-47)
* The library now detects executable files and sets the attribute (BEP-47)
  * Enabled by default. Set the new option `detectExec` to `false` to disable it
* The library now detects symlinks (BEP-47)
  * Disabled by default. Set the new option `detectSymlinks` to `true` to enable it
* The library now allows aligning files to piece boundaries (BEP-47)
  * Disabled by default. Set the new option `pieceAlign` to `true` or minimum bytes value to enable it

## 2.1.2

*Sep 25, 2021*

* Allow bencode 3.0, bump requirements to bencode 1.7+/2.7+/3.0+

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
