<?php

/**
 * projectname: metaverse/feedparser
 *
 * @package metaverse/feedparser
 */

namespace FeedParser\Plugin;

/**
 * FeedParser Syndication Extension Plugin
 *
 * The Syndication namespace is designed to give RSS feed readers and aggregators hints as to how often a feed is typically updated.
 *
 * @source http://www.feedforall.com/syndication.htm
 */
class SY extends \FeedParser\Plugin\Plugin
{
  private $updatePeriod = 0;
  private $updateFrequency = 0;
  private $updateBase = null;

  private function processData(\FeedParser\Base $feedbase, $meta_key, \SimpleXMLElement $meta_value)
  {
    switch ((string)$meta_key) {
      case 'updatePeriod':
        switch ($meta_value) {
          case 'hourly':
            $this->updatePeriod = 3600;
            break;
          case 'daily':
            $this->updatePeriod = 86400;
            break;
          case 'weekly':
            $this->updatePeriod = 604800;
            break;
          case 'monthly':
            $this->updatePeriod = 2592000;
            break;
          case 'yearly':
            $this->updatePeriod = 31536000;
            break;
          default:
            throw new \Exception('unknown sy.' . $meta_key . ': "' . $meta_value . '"');
        }
        break;
      case 'updateFrequency':
        if (is_numeric((int)$meta_value)) {
          $this->updateFrequency = (int)$meta_value;
        }
        break;
      case 'updateBase':
        $this->updateBase = new \DateTime((string)$meta_value);
        break;
    }
  }

  public function applyMetaData(\FeedParser\Base $feedbase)
  {
    if (isset($this->updatePeriod) && $this->updatePeriod > 0 && isset($this->updateFrequency) && $this->updateFrequency > 0) {
      $feedbase->updateFrequency = abs($this->updateFrequency * $this->updatePeriod);
    }
  }

  public function processMetaData(\FeedParser\Base $feedbase, $meta_namespace, $meta_key, \SimpleXMLElement $meta_value)
  {
    if($feedbase instanceof \FeedParser\Feed) {
      switch ((string)$meta_namespace) {
        case 'sy':
          $this->processData($feedbase, $meta_key, $meta_value);
          break;
      }
    }
    unset($meta_namespace, $meta_key, $meta_value);
  }
}
