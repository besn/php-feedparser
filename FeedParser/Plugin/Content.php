<?php

namespace FeedParser\Plugin;

use FeedParser\Base;
use FeedParser\Feed;
use FeedParser\Item;
use SimpleXMLElement;

/**
 * FeedParser Content Namespace Plugin
 *
 * The purpose of the Content namespace is to include the actual content of websites in an RSS feed.
 *
 * It typically contains an enhanced version of the channel or item's description.
 * This is typically an HTML version of the description that is either entity encoded or CDATA escaped.
 *
 * @source http://www.feedforall.com/content.htm
 */
class Content extends Plugin
{
  private $description = null;

  private function processData(Base $feedbase, $meta_key, SimpleXMLElement $meta_value)
  {
    switch ((string)$meta_key)
    {
      case 'encoded':
        $this->description = html_entity_decode((string)$meta_value);
        break;
    }
  }

  public function applyMetaData(Base $feedbase)
  {
    if (isset($this->description))
    {
      $feedbase->description = $this->description;
    }
  }

  public function processMetaData(Base $feedbase, $meta_namespace, $meta_key, SimpleXMLElement $meta_value)
  {
    if ($feedbase instanceof Feed)
    {
      switch ((string)$meta_namespace)
      {
        case 'content':
          $this->processData($feedbase, $meta_key, $meta_value);
          break;
      }
    }
    if ($feedbase instanceof Item)
    {
      switch ((string)$meta_namespace)
      {
        case 'content':
          $this->processData($feedbase, $meta_key, $meta_value);
          break;
      }
    }
    unset($meta_namespace, $meta_key, $meta_value);
  }
}
