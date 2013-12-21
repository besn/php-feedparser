<?php

/**
 * projectname: metaverse/feedparser
 *
 * @package metaverse/feedparser
 */

namespace FeedParser\Plugin;

/**
 * FeedParser Dublin Core Plugin
 *
 * The Dublin Core namespace allows for meta data to be associated with content.
 *
 * @source http://www.feedforall.com/dublin-core.htm
 */
class DC extends \FeedParser\Plugin\Plugin
{
  private $creator = null;
  private $title = null;
  private $date = null;

  private function processData(\FeedParser\FeedBase $feedbase, $meta_key, $meta_value)
  {
    switch ((string)$meta_key) {
      case 'creator': // The primary individual responsible for the content of the resource.
        $this->creator = (string)$meta_value;
        break;
      case 'title': // Title by which the resource is known.
        $this->title = (string)$meta_value;
        break;
      case 'date': // Defines the publication date for the resource.
        $this->date = new \DateTime((string)$meta_value);
        $this->date->setTimezone(new \DateTimeZone('UTC'));
        break;
    }
  }

  public function applyMetaData(\FeedParser\FeedBase $feedbase)
  {
    if (isset($this->creator) && !isset($feedbase->author)) {
      $feedbase->author = $this->creator;
    }
    if (isset($this->title) && !isset($feedbase->title)) {
      $feedbase->title = $this->title;
    }
    if (isset($this->date) && !isset($feedbase->time)) {
      $feedbase->time = $this->date;
    }
  }

  public function processMetaData(\FeedParser\FeedBase $feedbase, $meta_namespace, $meta_key, $meta_value)
  {
    switch ((string)$feedbase->meta_type) {
      case 'channel':
        switch ((string)$meta_namespace) {
          case 'dc':
            $this->processData($feedbase, $meta_key, $meta_value);
            break;
        }
        break;

      case 'item':
        switch ((string)$meta_namespace) {
          case 'dc':
            $this->processData($feedbase, $meta_key, $meta_value);
            break;
        }
        break;
    }
    unset($meta_namespace, $meta_key, $meta_value);
  }
}
