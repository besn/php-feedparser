<?php

/**
 * projectname: metaverse/feedparser
 *
 * @package metaverse/feedparser
 */

namespace FeedParser\Plugin;

/**
 * MediaRSS Plugin
 *
 *
 *
 * @source
 */
class Media extends \FeedParser\Plugin\Plugin
{
  private $title = null;
  private $thumbnail = null;
  private $keywords = null;
  private $player = null;

  private $media_attachments = array();

  private function processData(\FeedParser\Base $feedbase, $meta_key, $meta_value)
  {
    switch ((string)$meta_key) {
      case 'title':
      case 'keywords':
      case 'player':
        $this->$meta_key = (string)$meta_value;
        break;
      case 'thumbnail':
        $this->$meta_key = (string)$meta_value['url'];
        break;
      case 'content':
        $media_content = array();
        foreach($meta_value->attributes() as $sub_meta_key => $sub_meta_value) {
          $media_content[$sub_meta_key] = (string)$sub_meta_value;
        }
        if(isset($media_content['url'])) {
          $this->media_attachments[sha1($media_content['url'])] = $media_content;
        }
        break;
    }
  }

  public function applyMetaData(\FeedParser\Base $feedbase)
  {
    if (isset($this->title) && !isset($feedbase->title)) {
      $feedbase->title = $this->title;
    }
    $feedbase->media['thumbnail'] = $this->thumbnail;
    $feedbase->media['keywords'] = $this->keywords;
    $feedbase->media['player'] = $this->player;
    $feedbase->media['group'] = $this->media_attachments;
  }

  public function processMetaData(\FeedParser\Base $feedbase, $meta_namespace, $meta_key, $meta_value)
  {
    switch ((string)$feedbase->meta_type) {
      case 'item':
        switch ((string)$meta_namespace) {
          case 'media':
            switch ((string)$meta_key) {
              case 'group':
                if (count($meta_value->children($meta_namespace, true)) > 0) {
                  foreach ($meta_value->children($meta_namespace, true) as $sub_meta_key => $sub_meta_value) {
                    $this->processMetaData($feedbase, $meta_namespace, $sub_meta_key, $sub_meta_value);
                  }
                }
                break;

              default:
                $this->processData($feedbase, $meta_key, $meta_value);
                break;
            }
            break;
        }
        break;
    }
    unset($meta_namespace, $meta_key, $meta_value);
  }
}
