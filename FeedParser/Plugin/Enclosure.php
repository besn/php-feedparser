<?php

/**
 * projectname: metaverse/feedparser
 *
 * @package metaverse/feedparser
 */

namespace FeedParser\Plugin;

/**
 * Enclosure Plugin
 */
class Enclosure extends \FeedParser\Plugin\Plugin
{
  private $enclosure_id = null;
  private $enclosure = array();

  private function processData(\FeedParser\Base $feedbase, $meta_key, \SimpleXMLElement $meta_value)
  {
    switch ((string)$meta_key)
    {
      case 'enclosure':
        $this->enclosure_id = sha1((string)$meta_value['url']);
        $this->enclosure['url'] = (string)$meta_value['url'];
        $this->enclosure['length'] = (string)$meta_value['length'];
        $this->enclosure['type'] = (string)$meta_value['type'];
        break;
    }
  }

  public function applyMetaData(\FeedParser\Base $feedbase)
  {
    if (count($this->enclosure) > 0)
    {
      $feedbase->enclosures[$this->enclosure_id] = $this->enclosure;
    }
  }

  public function processMetaData(\FeedParser\Base $feedbase, $meta_namespace, $meta_key, \SimpleXMLElement $meta_value)
  {
    if ($feedbase instanceof \FeedParser\Item)
    {
      switch ((string)$meta_key)
      {
        case 'enclosure':
          $this->processData($feedbase, $meta_key, $meta_value);
          break;
      }
    }
    unset($meta_namespace, $meta_key, $meta_value);
  }
}
