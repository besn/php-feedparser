<?php

namespace FeedParser;

use Exception;
use RuntimeException;

/**
 * RSS/Atom/RDF FeedParser
 *
 * (c) Andreas Mery <besn@besn.at>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package FeedParser
 */
class FeedParser
{
    public const FEEDPARSER_TYPE_RDF = 'rdf';
    public const FEEDPARSER_TYPE_RSS = 'rss';
    public const FEEDPARSER_TYPE_ATOM = 'atom';

    public static $plugins = [];

    public function __construct()
    {
        /**
         * setup the plugins
         */
        if (is_dir(__DIR__.'/Plugin/')) {
            foreach (glob(__DIR__.'/Plugin/*.php') as $plugin_file) {
                $plugin_class = str_replace('.php', '', basename($plugin_file));
                if ($plugin_class === 'Plugin') {
                    continue;
                }

                self::$plugins[strtolower($plugin_class)]
                    = 'FeedParser\Plugin\\'.$plugin_class;
            }
        }
    }

    /**
     * Parses a RDF, RSS or Atom feed and returns a \Feedparser\Feed object
     *
     * @param string $feed_content
     *
     * @return Feed
     * @throws Exception
     */
    public function parse($feed_content): Feed
    {
        if ($feed_content === null) {
            throw new RuntimeException('missing feed content');
        }

        $feed_content_charset = mb_detect_encoding(
            $feed_content,
            mb_detect_order(),
            true
        );
        if ($feed_content_charset !== 'UTF-8') {
            $feed_content = iconv(
                $feed_content_charset,
                'UTF-8//IGNORE',
                $feed_content
            );
            if ( ! $feed_content) {
                throw new RuntimeException(
                    'error reading feed content as UTF-8'
                );
            }
        }

        $feed_content_charset = mb_detect_encoding(
            $feed_content,
            mb_detect_order(),
            true
        );
        if ( ! $feed_content_charset) {
            throw new RuntimeException('error reading feed content charset');
        }

        $feed_xml = simplexml_load_string($feed_content);
        if ( ! $feed_xml) {
            throw new RuntimeException('error parsing feed content');
        }

        return new Feed($feed_xml);
    }
}
