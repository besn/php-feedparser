<?php

/**
 * projectname: metaverse/feedparser
 *
 * @package metaverse/feedparser
 */

namespace FeedParser;

/**
 * Class Item
 */
class Item extends \FeedParser\Base
{
  /**
   * @var string Enclosed media
   */
  public $enclosures = array();

  /**
   * @var string Enclosed media (MediaRSS)
   */
  public $media = array();

  /**
   * @var string The time of publication of the item
   */
  public $time = null;

  /**
   * @var array The categories (tags) of the item
   */
  public $categories = array();

  /**
   * Initializes and parses a feed item
   *
   * @param int $feed_type The type of the feed (0: unknown, 1: rdf, 2: rss, 3: atom)
   * @param \SimpleXMLElement $item The \SimpleXMLElement of the feed item
   */
  public function __construct($feed_type, \SimpleXMLElement $item)
  {
    $this->setFeedType($feed_type);

    // initialize the plugins
    $p = array();
    foreach (\FeedParser\FeedParser::$plugins as $meta_key => $class_name)
    {
      $p[$meta_key] = new $class_name;
    }

    if (count($item->children()) > 0)
    {
      foreach ($item->children() as $meta_key => $meta_value)
      {
        if (isset($p[$meta_key]) && $p[$meta_key] instanceof \FeedParser\Plugin\Plugin)
        {
          $p[$meta_key]->processMetaData($this, '', $meta_key, $meta_value);
        }
        else
        {
          $p['core']->processMetaData($this, '', $meta_key, $meta_value);
        }
        unset($meta_key, $meta_value);
      }
    }

    // get the namespaces used in the item
    $namespaces = $item->getNamespaces(true);

    // go through the list of used namespaces
    foreach ($namespaces as $ns => $ns_uri)
    {
      if (count($item->children($ns, true)) > 0)
      {
        foreach ($item->children($ns, true) as $meta_key => $meta_value)
        {
          if (isset($p[$ns]) && $p[$ns] instanceof \FeedParser\Plugin\Plugin)
          {
            $p[$ns]->processMetaData($this, $ns, $meta_key, $meta_value);
          }
          else
          {
            $p['core']->processMetaData($this, $ns, $meta_key, $meta_value);
          }
          unset($meta_key, $meta_value);
        }
      }
      unset($ns, $ns_uri);
    }

    // apply the meta data
    foreach (\FeedParser\FeedParser::$plugins as $meta_key => $class_name)
    {
      $p[$meta_key]->applyMetaData($this);
    }
  }

  /**
   * Returns the time of publication of the item
   *
   * @return string
   */
  public function getTime()
  {
    return $this->time;
  }

  /**
   * Returns the ategories (tags) of the item
   *
   * @return array
   */
  public function getCategories()
  {
    return $this->categories;
  }

  /**
   * Sets the time of publication of the item
   *
   * @param string $time
   */
  public function setTime($time)
  {
    $this->time = $time;
  }

  /**
   * Adds an item category
   *
   * @param array $categories
   */
  public function addCategory($category)
  {
    $this->categories[] = $category;
  }
}
