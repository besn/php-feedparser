<?php

/**
 * projectname: metaverse/feedparser
 *
 * @package metaverse/feedparser
 */

namespace FeedParser;

// Unknown feed type
define('FEEDPARSER_TYPE_UNKNOWN', 0);
// RDF
define('FEEDPARSER_TYPE_RDF', 1);
// RSS 2.0
define('FEEDPARSER_TYPE_RSS', 2);
// ATOM 1.0
define('FEEDPARSER_TYPE_ATOM', 3);

/**
 * Parses a RSS or Atom Feed
 *
 * @param $feed_content
 * @return array
 * @throws \Exception
 */
class FeedParser
{
  public static $plugins = array();

  public function __construct()
  {
    // define the plugins
    if (is_dir(dirname(__FILE__) . '/Plugin/'))
    {
      foreach (glob(dirname(__FILE__) . '/Plugin/*.php') as $plugin_file)
      {
        $plugin_class = str_replace('.php', '', basename($plugin_file));
        self::$plugins[strtolower($plugin_class)] = 'FeedParser\Plugin\\' . $plugin_class;
      }
    }
  }

  /**
   * Parses a RDF, RSS or Atom feed and returns a \Feedparser\Feed object
   *
   * @param string $feed_content
   * @return \FeedParser\Feed
   * @throws \Exception
   */
  public function parse($feed_content)
  {
    if (!isset($feed_content))
    {
      throw new \Exception('missing feed content');
    }

    if (!($x = simplexml_load_string($feed_content)))
    {
      throw new \Exception('error parsing content');
    }

    return new \FeedParser\Feed($x, $this);
  }
}
