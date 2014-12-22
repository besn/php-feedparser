<?php

/**
 * projectname: metaverse/feedparser
 *
 * @package metaverse/feedparser
 */

namespace FeedParser\Plugin;

use FeedParser\Base;
use SimpleXMLElement;

class Plugin
{
  private function processData(Base $feedbase, $meta_key, SimpleXMLElement $meta_value)
  {
  }

  public function processMetaData(Base $feedbase, $meta_namespace, $meta_key, SimpleXMLElement $meta_value)
  {
  }

  public function applyMetaData(Base $feedbase)
  {
  }
}
