<?php

namespace FeedParser\Plugin;

use Exception;
use FeedParser\Base;
use FeedParser\Feed;
use RuntimeException;
use SimpleXMLElement;

/**
 * RSS/Atom/RDF FeedParser - SY plugin
 *
 * (c) Andreas Mery <besn@besn.at>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package FeedParser\Plugin
 */
class SY extends Plugin
{
    /**
     * @var int $updatePeriod
     */
    public $updatePeriod = 0;

    /**
     * @var int $updateFrequency
     */
    public $updateFrequency = 0;

    /**
     * @var \DateTime $updateBase
     */
    public $updateBase;

    /**
     * @param Base             $feedbase
     * @param string           $meta_namespace
     * @param string           $meta_key
     * @param SimpleXMLElement $meta_value
     *
     * @throws Exception
     */
    public function processMetaData(
        Base $feedbase,
        string $meta_namespace,
        string $meta_key,
        SimpleXMLElement $meta_value
    ): void {
        if ($meta_namespace !== 'sy') {
            return;
        }

        if ($feedbase instanceof Feed) {
            $this->processData($feedbase, $meta_key, $meta_value);
        }
    }

    /**
     * @param Base             $feedbase
     * @param string           $meta_key
     * @param SimpleXMLElement $meta_value
     *
     * @throws Exception
     */
    protected function processData(
        Base $feedbase,
        string $meta_key,
        SimpleXMLElement $meta_value
    ): void {
        switch ($meta_key) {
            case 'updatePeriod':
                switch ($meta_value) {
                    case 'hourly':
                        $this->updatePeriod = 3600;
                        break;

                    case 'daily':
                        $this->updatePeriod = 86400;
                        break;

                    case 'weekly':
                        $this->updatePeriod = 604800;
                        break;

                    case 'monthly':
                        $this->updatePeriod = 2592000;
                        break;

                    case 'yearly':
                        $this->updatePeriod = 31536000;
                        break;

                    default:
                        throw new RuntimeException(
                            'unknown sy.'.$meta_key.': "'.$meta_value.'"'
                        );
                }
                break;

            case 'updateFrequency':
                if (is_numeric((int)$meta_value)) {
                    $this->updateFrequency = (int)$meta_value;
                }
                break;

            case 'updateBase':
                $this->updateBase = new \DateTime(
                    (string)$meta_value,
                    new \DateTimeZone('UTC')
                );
                break;
        }
    }

    /**
     * @param Base $feedbase
     *
     * @throws Exception
     */
    public function applyMetaData(Base $feedbase): void
    {
        if ( ! ($feedbase instanceof Feed)) {
            return;
        }

        if ($this->updatePeriod !== null && $this->updatePeriod > 0
            && $this->updateFrequency !== null
            && $this->updateFrequency > 0
        ) {
            $feedbase->ttl = abs($this->updateFrequency * $this->updatePeriod);
        }
    }
}
