<?php

namespace FeedParser\Plugin;

use FeedParser\Base;
use FeedParser\Feed;
use FeedParser\Item;
use SimpleXMLElement;

/**
 * RSS/Atom/RDF FeedParser - Content plugin
 *
 * (c) Andreas Mery <besn@besn.at>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package FeedParser\Plugin
 */
class Content extends Plugin
{
    /**
     * @var string $description
     */
    public $description;

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
        if ($meta_namespace !== 'content') {
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
        if (strtolower($meta_key) === 'encoded') {
            $this->description = (string)$meta_value;
        }
    }

    /**
     * @param Base $feedbase
     */
    public function applyMetaData(Base $feedbase): void
    {
        if ($this->description !== null && $feedbase->description === null) {
            $feedbase->description = $this->description;
        }
    }
}
