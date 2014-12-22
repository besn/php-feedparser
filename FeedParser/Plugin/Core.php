<?php

/**
 * projectname: metaverse/feedparser
 *
 * @package metaverse/feedparser
 */

namespace FeedParser\Plugin;

use FeedParser\Base;
use FeedParser\Feed;
use FeedParser\Item;
use DateTime;
use DateTimeZone;
use SimpleXMLElement;

/**
 * Core Plugin
 */
class Core extends Plugin
{
  public function processMetaData(Base $feedbase, $meta_namespace, $meta_key, SimpleXMLElement $meta_value)
  {
    if ($feedbase instanceof Feed)
    {
      switch ((string)$meta_namespace)
      {
        case '':
          switch ($feedbase->getFeedType())
          {
            case FEEDPARSER_TYPE_RDF:
              switch (strtolower((string)$meta_key))
              {
                case 'title': // Defines the title of the channel (required)
                case 'link': // Defines the hyperlink to the channel (required)
                case 'description': // Describes the channel (required)
                  $feedbase->$meta_key = html_entity_decode((string)$meta_value);
                  break;
              }
              break;

            case FEEDPARSER_TYPE_RSS:
              switch (strtolower((string)$meta_key))
              {
                case 'title': // Defines the title of the channel (required)
                case 'link': // Defines the hyperlink to the channel (required)
                case 'description': // Describes the channel (required)
                  $feedbase->$meta_key = html_entity_decode((string)$meta_value);
                  break;

                case 'pubdate': // Defines the last publication date for the content of the feed (optional)
                  $feedbase->time = new DateTime((string)$meta_value);
                  $feedbase->time->setTimezone(new DateTimeZone('UTC'));
                  break;

                case 'ttl': // Specifies the number of minutes the feed can stay cached before refreshing it from the source (optional)
                  if (is_numeric((int)$meta_value))
                  {
                    $feedbase->updateFrequency = abs((int)$meta_value * 60);
                  }
                  break;

                case 'language':
                  $feedbase->language = (string)$meta_value;
                  break;
              }
              break;

            case FEEDPARSER_TYPE_ATOM:
              switch (strtolower((string)$meta_key))
              {
                case 'title': // Defines the title of the channel (required)
                  $feedbase->$meta_key = html_entity_decode((string)$meta_value);
                  break;

                case 'subtitle': // Describes the channel (required)
                  $feedbase->description = html_entity_decode((string)$meta_value);
                  break;

                case 'link': // Defines the hyperlink to the channel (required)
                  if (isset($meta_value['rel']) && $meta_value['rel'] == 'alternate' && isset($meta_value['type']) && $meta_value['type'] == 'text/html' && isset($meta_value['href']))
                  {
                    $feedbase->$meta_key = (string)$meta_value['href'];
                  }
                  break;

                case 'author': // Specifies the author of the item (optional)
                  if (isset($meta_value->name))
                  {
                    $feedbase->$meta_key = (string)$meta_value->name;
                  }
                  break;

                case 'updated': // Defines the last publication date for the content of the feed (optional)
                  $feedbase->time = new DateTime((string)$meta_value);
                  $feedbase->time->setTimezone(new DateTimeZone('UTC'));
                  break;
              }
              break;
          }
          break;
      }
    }
    if ($feedbase instanceof Item)
    {
      switch ((string)$meta_namespace)
      {
        case '':
          switch ($feedbase->getFeedType())
          {
            case FEEDPARSER_TYPE_RDF:
              switch (strtolower((string)$meta_key))
              {
                case 'title': // Defines the title of the item (required)
                case 'link': // Defines the hyperlink to the item (required)
                case 'description': // Describes the item (required)
                  $feedbase->$meta_key = html_entity_decode((string)$meta_value);
                  break;
              }
              break;

            case FEEDPARSER_TYPE_RSS:
              switch (strtolower((string)$meta_key))
              {
                case 'title': // Defines the title of the item (required)
                case 'link': // Defines the hyperlink to the item (required)
                case 'description': // Describes the item (required)
                case 'author': // Specifies the e-mail address to the author of the item (optional)
                  $feedbase->$meta_key = html_entity_decode((string)$meta_value);
                  break;

                case 'pubdate': // Defines the last publication date for the content of the feed (optional)
                  $feedbase->time = new DateTime((string)$meta_value);
                  $feedbase->time->setTimezone(new DateTimeZone('UTC'));
                  break;

                case 'category':
                  $feedbase->categories[] = (string)$meta_value;
                  break;
              }
              break;

            case FEEDPARSER_TYPE_ATOM:
              switch (strtolower((string)$meta_key))
              {
                case 'title': // Defines the title of the item (required)
                  $feedbase->$meta_key = html_entity_decode((string)$meta_value);
                  break;

                case 'summary': // Defines the item (required)
                  $feedbase->description = html_entity_decode((string)$meta_value);
                  break;

                case 'link': // Defines the hyperlink to the item (required)
                  if (isset($meta_value['rel']) && $meta_value['rel'] == 'alternate' && isset($meta_value['type']) && $meta_value['type'] == 'text/html' && isset($meta_value['href']))
                  {
                    $feedbase->$meta_key = (string)$meta_value['href'];
                  }
                  break;

                case 'author': // Specifies the author of the item (optional)
                  if (isset($meta_value->name))
                  {
                    $feedbase->$meta_key = (string)$meta_value->name;
                  }
                  break;

                case 'updated': // Defines the last publication date for the content of the feed (optional)
                  $feedbase->time = new DateTime((string)$meta_value);
                  $feedbase->time->setTimezone(new DateTimeZone('UTC'));
                  break;
              }
              break;
          }
          break;
      }
    }
    unset($meta_namespace, $meta_key, $meta_value);
  }
}
