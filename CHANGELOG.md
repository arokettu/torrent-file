# Changelog

## 1.1.1

_Oct 29, 2020_

* Fix notice on getComment() if comment is not set [[gh#5]]
* Fix `announce-list` not conforming to spec [[gh#4]]

[gh#4]: https://github.com/arokettu/torrent-file/pull/4/
[gh#5]: https://github.com/arokettu/torrent-file/pull/5/

## 1.1.0

_Oct 6, 2020_

* Expose options array to `fromFile()` call
* Fix `getAnnounceList()` is broken after `setAnnounceList()` is used 

## 1.0.2

_Oct 6, 2020_

* Fix torrent creation
* Fix info hash calculation

## 1.0.1

_Jul 21, 2020_

* Allow bencode v2 and Symfony v5

## 1.0.0

_Apr 19, 2019_

* Force announce list to be List
* Remove encoding field
* Release as 1.0.0 because it is quite stable

## 0.1.1

_Nov 6, 2017_

Update Symfony dependencies to allow Symfony 4

## 0.1.0

_Mar 30, 2017_

Initial release
Basic work with torrent files
