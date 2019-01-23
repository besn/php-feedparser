<?php

namespace FeedParser;

use FeedParser\Plugin\Plugin;
use SimpleXMLElement;

/**
 * RSS/Atom/RDF FeedParser - Base class
 *
 * (c) Andreas Mery <besn@besn.at>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package FeedParser
 */
class Base
{
    /**
     * @var SimpleXMLElement $rawXml
     */
    public $rawXml;

    /**
     * @var string $feedType
     */
    public $feedType;

    /**
     * @var string $title
     */
    public $title;

    /**
     * @var string $language
     */
    public $language;

    /**
     * @var string $link
     */
    public $link;

    /**
     * @var array $guid
     */
    public $guid;

    /**
     * @var string|array $description
     */
    public $description;

    /**
     * @var \DateTime $pubDate
     */
    public $pubDate;

    /**
     * @var \DateTime $updated
     */
    public $updated;

    /**
     * @var string $author
     */
    public $author;

    /**
     * @var array $categories
     */
    public $categories = [];

    /**
     * @var array $categories
     */
    public $plugins = [];

    /**
     * initialize the plugins
     */
    public function loadPlugins(): void
    {
        foreach (FeedParser::$plugins as $plugin => $class_name) {
            $this->plugins[$plugin] = new $class_name();
        }
    }

    /**
     * @param SimpleXMLElement $xml
     */
    public function process(SimpleXMLElement $xml): void
    {
        /**
         * process the children
         */
        if ($xml->children()->count() > 0) {
            foreach ($xml->children() as $element => $elementValue) {
                if (isset(FeedParser::$plugins[$element])
                    && FeedParser::$plugins[$element] instanceof Plugin
                ) {
                    $this->plugins[$element]->processMetaData(
                        $this,
                        '',
                        $element,
                        $elementValue
                    );
                } else {
                    $this->plugins['core']->processMetaData(
                        $this,
                        '',
                        $element,
                        $elementValue
                    );
                }
                unset($element, $elementValue);
            }
        }

        /**
         * process the namespaces
         */
        foreach ($xml->getNamespaces(true) as $namespace => $namespaceUri) {
            if ($xml->children($namespace, true)->count() > 0) {
                foreach (
                    $xml->children($namespace, true) as $element =>
                    $elementValue
                ) {
                    if (isset($this->plugins[$namespace])
                        && $this->plugins[$namespace] instanceof Plugin
                    ) {
                        $this->plugins[$namespace]->processMetaData(
                            $this,
                            $namespace,
                            $element,
                            $elementValue
                        );
                    } else {
                        $this->plugins['core']->processMetaData(
                            $this,
                            $namespace,
                            $element,
                            $elementValue
                        );
                    }
                    unset($element, $elementValue);
                }
            }
            unset($namespace, $namespaceUri);
        }

        /**
         * apply the meta data
         */
        foreach ($this->plugins as $pluginName => $plugin) {
            $plugin->applyMetaData($this);
        }
    }

    /**
     * @return SimpleXMLElement
     */
    public function getRawXml(): SimpleXMLElement
    {
        return $this->rawXml;
    }

    /**
     * @param SimpleXMLElement $rawXml
     */
    public function setRawXml(SimpleXMLElement $rawXml): void
    {
        $this->rawXml = $rawXml;
    }

    /**
     * @return string
     */
    public function getFeedType(): string
    {
        return $this->feedType;
    }

    /**
     * @param string $feedType
     */
    public function setFeedType(string $feedType): void
    {
        $this->feedType = $feedType;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getLanguage(): string
    {
        return $this->language;
    }

    /**
     * @param string $language
     */
    public function setLanguage(string $language): void
    {
        $this->language = $language;
    }

    /**
     * @return string
     */
    public function getLink(): string
    {
        return $this->link;
    }

    /**
     * @param string $link
     */
    public function setLink(string $link): void
    {
        $this->link = $link;
    }

    /**
     * @return array
     */
    public function getGuid(): array
    {
        return $this->guid;
    }

    /**
     * @param array $guid
     */
    public function setGuid(array $guid): void
    {
        $this->guid = $guid;
    }

    /**
     * @return array|string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param array|string $description
     */
    public function setDescription($description): void
    {
        $this->description = $description;
    }

    /**
     * @return \DateTime
     */
    public function getPubDate(): \DateTime
    {
        return $this->pubDate;
    }

    /**
     * @param \DateTime $pubDate
     */
    public function setPubDate(\DateTime $pubDate): void
    {
        $this->pubDate = $pubDate;
    }

    /**
     * @return \DateTime
     */
    public function getUpdated(): \DateTime
    {
        return $this->updated;
    }

    /**
     * @param \DateTime $updated
     */
    public function setUpdated(\DateTime $updated): void
    {
        $this->updated = $updated;
    }

    /**
     * @return string
     */
    public function getAuthor(): string
    {
        return $this->author;
    }

    /**
     * @param string $author
     */
    public function setAuthor(string $author): void
    {
        $this->author = $author;
    }

    /**
     * @return array
     */
    public function getCategories(): array
    {
        return $this->categories;
    }

    /**
     * @param array $categories
     */
    public function setCategories(array $categories): void
    {
        $this->categories = $categories;
    }
}
