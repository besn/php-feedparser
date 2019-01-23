<?php

namespace FeedParser\Plugin;

use FeedParser\Base;
use FeedParser\Item;
use SimpleXMLElement;

/**
 * RSS/Atom/RDF FeedParser - Media plugin
 *
 * (c) Andreas Mery <besn@besn.at>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package FeedParser\Plugin
 */
class Media extends Plugin
{
    /**
     * @var string $title
     */
    private $title;

    /**
     * @var string $thumbnail
     */
    private $thumbnail;

    /**
     * @var string $keywords
     */
    private $keywords;

    /**
     * @var string $player
     */
    private $player;

    /**
     * @var array $attachments
     */
    private $attachments = [];

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
        if ($meta_namespace !== 'media') {
            return;
        }

        if ($feedbase instanceof Item) {
            if ($meta_key === 'group') {
                if (\count($meta_value->children($meta_namespace, true))
                    > 0
                ) {
                    foreach (
                        $meta_value->children($meta_namespace, true) as
                        $sub_meta_key => $sub_meta_value
                    ) {
                        $this->processMetaData(
                            $feedbase,
                            $meta_namespace,
                            $sub_meta_key,
                            $sub_meta_value
                        );
                    }
                }
            } else {
                $this->processData($feedbase, $meta_key, $meta_value);
            }
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
        switch ($meta_key) {
            case 'title':
            case 'keywords':
            case 'player':
                $this->$meta_key = (string)$meta_value;
                break;
            case 'thumbnail':
                $this->$meta_key = (string)$meta_value['url'];
                break;
            case 'content':
                $media_content = [];
                foreach (
                    $meta_value->attributes() as $sub_meta_key =>
                    $sub_meta_value
                ) {
                    $media_content[$sub_meta_key] = (string)$sub_meta_value;
                }
                if (isset($media_content['url'])) {
                    $this->attachments[sha1($media_content['url'])]
                        = $media_content;
                }
                break;
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

        if ( ! empty($this->title)) {
            $feedbase->media['title'] = $this->title;
        }
        if ( ! empty($this->thumbnail)) {
            $feedbase->media['thumbnail'] = $this->thumbnail;
        }
        if ( ! empty($this->keywords)) {
            $feedbase->media['keywords'] = $this->keywords;
        }
        if ( ! empty($this->player)) {
            $feedbase->media['player'] = $this->player;
        }
        if (\count($this->attachments) > 0) {
            $feedbase->media['group'] = $this->attachments;
        }
    }
}
