<?php

namespace FeedParser;

use SimpleXMLElement;

/**
 * RSS/Atom/RDF FeedParser - Feed item class
 *
 * (c) Andreas Mery <besn@besn.at>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package FeedParser
 */
class Item extends Base
{
    /**
     * @var array $enclosures
     */
    public $enclosures = [];

    /**
     * @var array $media
     */
    public $media = [];

    /**
     * Initializes and parses a feed item
     *
     * @param string           $feed_type The type of the feed
     * @param SimpleXMLElement $itemXml   The \SimpleXMLElement of the feed item
     */
    public function __construct(string $feed_type, SimpleXMLElement $itemXml)
    {
        $this->loadPlugins();
        $this->setRawXml($itemXml);
        $this->setFeedType($feed_type);
        $this->process($itemXml);
    }

    /**
     * @return array
     */
    public function getEnclosures(): array
    {
        return $this->enclosures;
    }

    /**
     * @param array $enclosures
     */
    public function setEnclosures(array $enclosures): void
    {
        $this->enclosures = $enclosures;
    }

    /**
     * @return array
     */
    public function getMedia(): array
    {
        return $this->media;
    }

    /**
     * @param array $media
     */
    public function setMedia(array $media): void
    {
        $this->media = $media;
    }
}
