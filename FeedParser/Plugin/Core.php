<?php

namespace FeedParser\Plugin;

use DateTimeZone;
use FeedParser\Base;
use FeedParser\Feed;
use FeedParser\FeedParser;
use FeedParser\Item;
use SimpleXMLElement;

/**
 * RSS/Atom/RDF FeedParser - Core plugin
 *
 * (c) Andreas Mery <besn@besn.at>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package FeedParser\Plugin
 */
class Core extends Plugin
{
    /**
     * @var string $link
     */
    public $link;

    /**
     * @var string $language
     */
    public $language;

    /**
     * @var string $title
     */
    public $title;

    /**
     * @var int $ttl
     */
    public $ttl;

    /**
     * @var string $pubDate
     */
    public $pubDate;

    /**
     * @var string $updated
     */
    public $updated;

    /**
     * @var array $author
     */
    public $author = [];

    /**
     * @var array $categories
     */
    public $categories = [];

    /**
     * @var array $description
     */
    public $description = [];

    /**
     * @var array $generator
     */
    public $generator = [];

    /**
     * @var array $guid
     */
    public $guid = [];

    /**
     * @param Base             $feedbase
     * @param string           $meta_namespace
     * @param string           $meta_key
     * @param SimpleXMLElement $meta_value
     */
    public function processMetaData(
        Base $feedbase,
        string $meta_namespace,
        string $meta_key,
        SimpleXMLElement $meta_value
    ): void {
        if ($meta_namespace !== '') {
            return;
        }

        switch (true) {
            case ($feedbase instanceof Feed):
            case ($feedbase instanceof Item):
                $this->processData($feedbase, $meta_key, $meta_value);
                break;
        }
    }

    /**
     * @param Base             $feedbase
     * @param string           $meta_key
     * @param SimpleXMLElement $meta_value
     */
    protected function processData(
        Base $feedbase,
        string $meta_key,
        SimpleXMLElement $meta_value
    ): void {
        $metaKey = strtolower($meta_key);

        switch ($feedbase->getFeedType()) {
            case FeedParser::FEEDPARSER_TYPE_RDF:
                $this->processRdfData($feedbase, $metaKey, $meta_value);
                break;

            case FeedParser::FEEDPARSER_TYPE_RSS:
                $this->processRssData($feedbase, $metaKey, $meta_value);
                break;

            case FeedParser::FEEDPARSER_TYPE_ATOM:
                $this->processAtomData($feedbase, $metaKey, $meta_value);
                break;
        }
    }

    /**
     * @param Base             $feedbase
     * @param string           $metaKey
     * @param SimpleXMLElement $meta_value
     */
    public function processRdfData(
        Base $feedbase,
        string $metaKey,
        SimpleXMLElement $meta_value
    ): void {
        switch ($metaKey) {
            case 'title':
            case 'link':
                $this->$metaKey = (string)$meta_value;
                break;

            case 'description':
                $this->description['content'] = (string)$meta_value;
                break;
        }
    }

    /**
     * @param Base             $feedbase
     * @param string           $metaKey
     * @param SimpleXMLElement $meta_value
     */
    public function processRssData(
        Base $feedbase,
        string $metaKey,
        SimpleXMLElement $meta_value
    ): void {
        switch ($metaKey) {
            case 'title':
            case 'link':
            case 'language':
            case 'updated':
                if (empty($this->$metaKey)) {
                    $this->$metaKey = (string)$meta_value;
                }
                break;

            case 'description':
                $this->description = html_entity_decode(
                    trim((string)$meta_value)
                );
                break;

            case 'author':
                if (empty($author['email'])) {
                    $author['email'] = (string)$meta_value;
                }
                break;

            case 'pubdate':
            case 'lastbuilddate':
                if (empty($this->pubDate)) {
                    $this->pubDate = (string)$meta_value;
                }
                break;

            case 'ttl':
                if (is_numeric((int)$meta_value)) {
                    $this->$metaKey = abs((int)$meta_value * 60);
                }
                break;

            case 'skiphours':
                if (is_numeric((int)$meta_value)) {
                    $this->$metaKey = abs((int)$meta_value * 3600);
                }
                break;

            case 'skipdays':
                if (is_numeric((int)$meta_value)) {
                    /** @noinspection SummerTimeUnsafeTimeManipulationInspection */
                    $this->$metaKey = abs((int)$meta_value * 86400);
                }
                break;

            case 'guid':
                $this->$metaKey = [
                    'id' => (string)$meta_value,
                    'permaLink' => $meta_value['isPermaLink'] !== null,
                ];
                break;

            case 'category':
                $this->categories[] = (string)$meta_value;
                break;
        }
    }

    /**
     * @param Base             $feedbase
     * @param string           $metaKey
     * @param SimpleXMLElement $meta_value
     */
    public function processAtomData(
        Base $feedbase,
        string $metaKey,
        SimpleXMLElement $meta_value
    ): void {
        switch ($metaKey) {
            case 'id':
                $this->guid = [
                    'id' => (string)$meta_value,
                    'permaLink' => $meta_value['isPermaLink'] !== null
                        || strpos((string)$meta_value, 'http') === 0,
                ];
                break;

            case 'title':
            case 'updated':
                if (empty($this->$metaKey)) {
                    $this->$metaKey = (string)$meta_value;
                }
                break;

            case 'subtitle':
                if (empty($this->description)) {
                    $this->description = html_entity_decode(
                        trim((string)$meta_value)
                    );
                }
                break;

            case 'summary':
                if ( ! isset($this->description[$metaKey])) {
                    $this->description[$metaKey] = html_entity_decode(
                        trim((string)$meta_value)
                    );
                }
                break;

            case 'content':
                $contentType = (string)$meta_value['type'] ?: 'html';
                if ( ! isset($this->description[$metaKey][$contentType])) {
                    $this->description[$metaKey][$contentType]
                        = html_entity_decode(trim((string)$meta_value));
                }
                break;

            case 'link':
                if (isset($meta_value['rel'], $meta_value['href'])
                && $meta_value['rel'] === ($feedbase instanceof Feed) ? 'self'
                    : 'alternate'
                ) {
                    $this->$metaKey = (string)$meta_value['href'];
                }
                break;

            case 'author':
                $author = [];
                if (isset($meta_value->name)) {
                    $author['name'] = (string)$meta_value->name;
                }
                if (isset($meta_value->email)) {
                    $author['email'] = (string)$meta_value->email;
                }
                if (isset($meta_value->uri)) {
                    $author['uri'] = (string)$meta_value->uri;
                }
                if (\count($author) > 0) {
                    $this->$metaKey = $author;
                }
                break;

            case 'generator':
                $generator = [];
                $generator['name'] = (string)$meta_value;
                if (isset($meta_value->uri)) {
                    $generator['uri'] = (string)$meta_value['uri'];
                }
                if (isset($meta_value->version)) {
                    $generator['version'] = (string)$meta_value['version'];
                }
                if (\count($generator) > 0) {
                    $this->$metaKey = $generator;
                }
                break;

            case 'published':
                if (empty($this->pubDate)) {
                    $this->pubDate = (string)$meta_value;
                }
                break;

            case 'category':
                $this->categories[] = (string)$meta_value['term'];
                break;
        }
    }

    /**
     * @param Base $feedbase
     *
     * @throws \Exception
     */
    public function applyMetaData(Base $feedbase): void
    {
        foreach (
            [
                'guid',
                'title',
                'link',
                'description',
                'pubDate',
                'updated',
                'author',
                'generator',
                'categories',
                'language',
            ] as $field
        ) {
            if (
                (isset($this->$field) && $this->$field !== null) &&
                (!isset($feedbase->$field) || $feedbase->$field === null)
            ) {
                switch ($field) {
                    case 'title':
                    case 'link':
                        $feedbase->$field = html_entity_decode(
                            strip_tags(trim($this->$field))
                        );
                        break;

                    case 'author':
                        if (\count($this->$field) === 0) {
                            break;
                        }
                        if (isset($this->$field['name'])) {
                            $feedbase->$field = $this->$field['name'].' <'
                                .$this->$field['email'].'>';
                            break;
                        }

                        $feedbase->$field = $this->$field['email'];
                        break;

                    case 'generator':
                        if (\count($this->$field) === 0) {
                            break;
                        }
                        if (isset($this->$field['version'])) {
                            $feedbase->$field = $this->$field['name'].'/'
                                .$this->$field['version'];
                            break;
                        }

                        $feedbase->$field = $this->$field['name'];
                        break;

                    case 'categories':
                        if (\count($this->$field) === 0) {
                            break;
                        }
                        if (isset($this->$field['version'])) {
                            $feedbase->$field = $this->$field['name'].'/'
                                .$this->$field['version'];
                            break;
                        }

                        $feedbase->$field = $this->$field['name'];
                        break;

                    case 'pubDate':
                    case 'updated':
                        $feedbase->$field = new \DateTime(
                            $this->$field,
                            new DateTimeZone('UTC')
                        );
                        break;

                    default:
                        $feedbase->$field = $this->$field;
                        break;
                }
            }
        }
    }
}
