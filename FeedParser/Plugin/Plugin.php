<?php

namespace FeedParser\Plugin;

use FeedParser\Base;
use SimpleXMLElement;

/**
 * RSS/Atom/RDF FeedParser - Plugin base
 *
 * (c) Andreas Mery <besn@besn.at>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package FeedParser\Plugin
 */
abstract class Plugin
{
    /**
     * @param Base             $feedbase
     * @param string           $meta_namespace
     * @param string           $meta_key
     * @param SimpleXMLElement $meta_value
     */
    abstract public function processMetaData(
        Base $feedbase,
        string $meta_namespace,
        string $meta_key,
        SimpleXMLElement $meta_value
    ): void;

    /**
     * @param Base $feedbase
     */
    abstract public function applyMetaData(Base $feedbase): void;

    /**
     * @param Base             $feedbase
     * @param string           $meta_key
     * @param SimpleXMLElement $meta_value
     */
    abstract protected function processData(
        Base $feedbase,
        string $meta_key,
        SimpleXMLElement $meta_value
    ): void;
}
