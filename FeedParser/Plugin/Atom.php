<?php

namespace FeedParser\Plugin;

use FeedParser\Base;
use FeedParser\Feed;
use FeedParser\Item;
use SimpleXMLElement;

/**
 * RSS/Atom/RDF FeedParser - Atom plugin
 *
 * (c) Andreas Mery <besn@besn.at>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package FeedParser\Plugin
 */
class Atom extends Core
{
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
        if ($meta_namespace !== 'atom') {
            return;
        }

        switch (true) {
            case ($feedbase instanceof Feed):
            case ($feedbase instanceof Item):
                $this->processAtomData(
                    $feedbase,
                    strtolower($meta_key),
                    $meta_value
                );
                break;
        }
    }
}
