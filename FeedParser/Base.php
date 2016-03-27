<?php

namespace FeedParser;

/**
 * Class Base
 */
class Base
{
  /**
   * @var int The type of the feed (0: unknown, 1: rdf, 2: rss, 3: atom)
   */
  private $feed_type = null;

  /**
   * @var string The title of the feed or item
   */
  public $title = null;

  /**
   * @var string The link to the feed or item
   */
  public $link = null;

  /**
   * @var string The description of the feed or item
   */
  public $description = null;

  /**
   * @var string The author of the feed or item
   */
  public $author = null;

  /**
   * Returns the title of the feed or item
   *
   * @return string
   */
  public function getTitle()
  {
    return $this->title;
  }

  /**
   * Returns the author of the feed or item
   *
   * @return string
   */
  public function getAuthor()
  {
    return $this->author;
  }

  /**
   * Returns the description of the feed or item
   *
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }

  /**
   * Returns the type of the feed (0: unknown, 1: rdf, 2: rss, 3: atom)
   *
   * @return int
   */
  public function getFeedType()
  {
    return $this->feed_type;
  }

  /**
   * Returns the link to the feed or item
   *
   * @return string
   */
  public function getLink()
  {
    return $this->link;
  }

  /**
   * Sets the author of the feed or item
   *
   * @param string $author
   */
  public function setAuthor($author)
  {
    $this->author = $author;
  }

  /**
   * Sets the description of the feed or item
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }

  /**
   * Sets the type of the feed (0: unknown, 1: rdf, 2: rss, 3: atom)
   *
   * @param int $feed_type
   */
  public function setFeedType($feed_type)
  {
    $this->feed_type = $feed_type;
  }

  /**
   * Sets the link to the feed or item
   *
   * @param string $link
   */
  public function setLink($link)
  {
    $this->link = $link;
  }

  /**
   * Sets the title of the feed or item
   *
   * @param string $title
   */
  public function setTitle($title)
  {
    $this->title = $title;
  }
}
