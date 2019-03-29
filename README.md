# plejeune/video-bundle

This bundle, in relation with the plejeune/api-bundle, call available API in order to retrieve videos from registered channels.

## TODO 
* [YOUTUBE] On video retrieve, detect if the video is type live. If so, generate ```php new IsLive(Video $video) ``` event