<?php
include('youtube_uploader.php');

$params = array
(
    'title' => 'title example',
    'description' => 'description example',
    'tags' => array
    (
        'tag1',
        'tag2'
    ),
    'status' => 'private',
    'video_path' => 'example.webm'
);
$y = new YoutubeUploader($params);
$y->upload();

