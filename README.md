# plejeune/video-bundle

This bundle, in relation with the plejeune/api-bundle, call available API in order to retrieve videos from registered channels.

## Features
* [YOUTUBE] On video retrieve, detect if the video is type live. If so, generate ```php new IsLiveEvent() ``` event
