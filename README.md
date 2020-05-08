# darkanakin41/video-bundle

[![Actions Status](https://github.com/darkanakin41/video-bundle/workflows/Quality/badge.svg)](https://github.com/darkanakin41/video-bundle/actions)
[![Total Downloads](https://poser.pugx.org/darkanakin41/video-bundle/downloads.svg)](https://packagist.org/packages/darkanakin41/video-bundle) 
[![Latest Stable Version](https://poser.pugx.org/darkanakin41/video-bundle/v/stable.svg)](https://packagist.org/packages/darkanakin41/video-bundle)

This bundle, in relation with the darkanakin41/api-bundle, call available API in order to retrieve videos from registered channels.

## Features
* [YOUTUBE] On video retrieve, detect if the video is type live. If so, generate ```php new IsLiveEvent() ``` event
