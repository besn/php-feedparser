<?php

namespace FeedParser\Plugin;

use DateTime;
use DateTimeZone;
use FeedParser\Base;
use FeedParser\Feed;
use FeedParser\Item;
use SimpleXMLElement;

/**
 * RSS/Atom/RDF FeedParser - DC plugin
 *
 * (c) Andreas Mery <besn@besn.at>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package FeedParser\Plugin
 */
class DC extends Plugin
{
    /**
     * @var string $author
     */
    public $author;

    /**
     * @var string $title
     */
    public $title;

    /**
     * @var string $pubDate
     */
    public $pubDate;

    /**
     * @param Base             $feedbase
     * @param                  $meta_namespace
     * @param string           $meta_key
     * @param SimpleXMLElement $meta_value
     */
    public function processMetaData(
        Base $feedbase,
        string $meta_namespace,
        string $meta_key,
        SimpleXMLElement $meta_value
    ): void {
        if ($meta_namespace !== 'dc') {
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
        switch ($metaKey) {
            case 'title':
                $this->$metaKey = (string)$meta_value;
                break;

            case 'creator':
                $this->author = (string)$meta_value;
                break;

            case 'date':
                $this->pubDate = (string)$meta_value;
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
        foreach (['title', 'pubDate', 'author'] as $field) {
            if ($this->$field !== null && $feedbase->$field === null) {
                switch ($field) {
                    case 'title':
                    case 'link':
                    case 'author':
                        $feedbase->$field = html_entity_decode(
                            strip_tags(trim($this->$field))
                        );
                        break;

                    case 'description':
                        $feedbase->$field = html_entity_decode(
                            trim($this->$field)
                        );
                        break;

                    case 'pubdate':
                        $feedbase->$field = new DateTime(
                            $feedbase->$field,
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
