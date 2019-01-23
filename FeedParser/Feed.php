<?php

namespace FeedParser;

use Exception;
use LogicException;
use SimpleXMLElement;

/**
 * RSS/Atom/RDF FeedParser - Feed class
 *
 * (c) Andreas Mery <besn@besn.at>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package FeedParser
 */
class Feed extends Base
{
    /**
     * @var string $generator
     */
    public $generator;

    /**
     * @var array $items
     */
    public $items = [];

    /**
     * @var int $ttl
     */
    public $ttl = -1;

    /**
     * @param SimpleXMLElement $feedXml
     */
    public function __construct(SimpleXMLElement $feedXml)
    {
        $this->loadPlugins();
        $this->process($feedXml);
    }

    /**
     * @param SimpleXMLElement $feedXml
     */
    public function process(SimpleXMLElement $feedXml): void
    {
        $this->setRawXml($feedXml);
        $this->setFeedType($this->detectFeedType($feedXml));

        $channel = null;
        $items = null;

        switch ($this->getFeedType()) {
            case FeedParser::FEEDPARSER_TYPE_RDF:
                /** @noinspection PhpUndefinedFieldInspection */
                $channel = $feedXml->channel;
                /** @noinspection PhpUndefinedFieldInspection */
                $items = $feedXml->item;
                break;

            case FeedParser::FEEDPARSER_TYPE_RSS:
                /** @noinspection PhpUndefinedFieldInspection */
                $channel = $feedXml->channel;
                /** @noinspection PhpUndefinedFieldInspection */
                $items = $feedXml->channel->item;
                break;

            case FeedParser::FEEDPARSER_TYPE_ATOM:
                $channel = $feedXml;
                /** @noinspection PhpUndefinedFieldInspection */
                $items = $feedXml->entry;
                break;

            default:
                throw new \RuntimeException('Unknown feed type');
                break;
        }

        /**
         * process the channel
         */
        parent::process($channel);

        /**
         * process the feed items
         */
        foreach ($items as $item) {
            $this->items[] = new Item($this->getFeedType(), $item);
        }
    }

    /**
     * Tries to find out the type of feed and returns it
     *
     * @param SimpleXMLElement $x
     *
     * @return string
     * @throws LogicException
     */
    public function detectFeedType(SimpleXMLElement $x): string
    {
        switch (strtolower($x->getName())) {
            case 'rdf':
                return FeedParser::FEEDPARSER_TYPE_RDF;
                break;

            case 'rss':
                return FeedParser::FEEDPARSER_TYPE_RSS;
                break;

            case 'feed':
                return FeedParser::FEEDPARSER_TYPE_ATOM;
                break;

            default:
                throw new LogicException('Unknown feed type');
                break;
        }
    }

    /**
     * @return string
     */
    public function getGenerator(): string
    {
        return $this->generator;
    }

    /**
     * @param string $generator
     */
    public function setGenerator(string $generator): void
    {
        $this->generator = $generator;
    }

    /**
     * @return int
     */
    public function getTtl(): int
    {
        return $this->ttl;
    }

    /**
     * @param int $ttl
     *
     * @throws Exception
     */
    public function setTtl(int $ttl): void
    {
        if ( ! is_numeric($ttl) || $ttl <= 0) {
            throw new \LogicException('invalid time to live');
        }

        $this->ttl = $ttl;
    }

    /**
     * @return array
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @param array $items
     */
    public function setItems(array $items): void
    {
        $this->items = $items;
    }
}
