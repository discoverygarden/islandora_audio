# Audio Solution Pack

## Introduction

Adds all required Fedora objects to allow users to ingest and retrieve audio
files through the Islandora interface.

## Requirements

This module requires the following modules/libraries:

* [Islandora](https://github.com/discoverygarden/islandora)
* [Tuque](https://github.com/islandora/tuque)
* [Lame](http://lame.sourceforge.net)(Debian/Ubuntu `sudo apt-get install lame`)

## Installation

Install as
[usual](https://www.drupal.org/docs/8/extending-drupal-8/installing-drupal-8-modules).

## Configuration

### Derivative Configuration

A set of options exist for configuring derivatives for audio objects. 

* Defer (do not create) derivatives for audio objects during ingest. You may
find this useful if derivatives are being created by an external service,
or if they are not required. This is specific to audio objects, but if 
derivatives are deferred for all Islandora objects (an option under 
admin/islandora/configure) then audio derivatives will not be created 
regardless of how this option is set.
* Set the path for `lame`. MP3 derivatives are created using the `lame` 
command which must be installed as described above.
* MP3 derivative quality can be configured if high-quality (or low-size)
 derivatives are desired. Note that numbers closer to 0 will result in 
 larger, higher-quality audio files.
* Use original file as fallback will allow the OBJ to be played in a 
player if a derivative cannot be found, and the original file is a 
format that can be played. This, in conjunction with defering audio 
derivatives, may save space in the repository and improve playback 
quality if, for example, you are ingesting a collection of MP3 files. 
 
 ### Viewer Configuration

Select the desired viewer for audio objects. Current viewer that can 
be configured is:

* [Islandora Video.js](https://github.com/discoverygarden/islandora_videojs)

![Configuration](https://cloud.githubusercontent.com/assets/1943338/18892256/42a78df0-84df-11e6-8e51-6b67c1a8c81a.png)

## Documentation

Further documentation for this module is available at
[our wiki](https://wiki.duraspace.org/display/ISLANDORA/Audio+Solution+Pack).

## Troubleshooting/Issues

Having problems or solved one? Create an issue, check out the Islandora Google
groups.

* [Users](https://groups.google.com/forum/?hl=en&fromgroups#!forum/islandora)
* [Devs](https://groups.google.com/forum/?hl=en&fromgroups#!forum/islandora-dev)

or contact [discoverygarden](http://support.discoverygarden.ca).

## Maintainers/Sponsors

Current maintainers:

* [discoverygarden](http://www.discoverygarden.ca)

## Development

If you would like to contribute to this module, please check out the helpful
[Documentation](https://github.com/Islandora/islandora/wiki#wiki-documentation-for-developers),
[Developers](http://islandora.ca/developers) section on Islandora.ca and create
an issue, pull request and or contact
[discoverygarden](http://support.discoverygarden.ca).

## License

[GPLv3](http://www.gnu.org/licenses/gpl-3.0.txt)
