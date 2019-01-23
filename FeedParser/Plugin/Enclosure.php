<?php

namespace FeedParser\Plugin;

use FeedParser\Base;
use FeedParser\Item;
use SimpleXMLElement;

/**
 * RSS/Atom/RDF FeedParser - Enclosure plugin
 *
 * (c) Andreas Mery <besn@besn.at>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package FeedParser\Plugin
 */
class Enclosure extends Plugin
{
    private $enclosure_id;
    private $enclosure = [];

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
        if ($meta_namespace !== 'enclosure') {
            return;
        }

        if ($feedbase instanceof Item) {
            $this->processData($feedbase, $meta_key, $meta_value);
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
        if ($meta_key === 'enclosure') {
            $this->enclosure_id = sha1((string)$meta_value['url']);
            $this->enclosure['url'] = (string)$meta_value['url'];
            $this->enclosure['length'] = (string)$meta_value['length'];
            $this->enclosure['type'] = (string)$meta_value['type'];
        }
    }

    /**
     * @param Base $feedbase
     */
    public function applyMetaData(Base $feedbase): void
    {
        if ( ! ($feedbase instanceof Item)) {
            return;
        }

        if (\count($this->enclosure) > 0) {
            $feedbase->enclosures[$this->enclosure_id] = $this->enclosure;
        }
    }
}
