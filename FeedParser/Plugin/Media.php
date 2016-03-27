<?php

namespace FeedParser\Plugin;

use FeedParser\Base;
use FeedParser\Item;
use SimpleXMLElement;

/**
 * MediaRSS Plugin
 *
 *
 *
 * @source
 */
class Media extends Plugin
{
  private $title = null;
  private $thumbnail = null;
  private $keywords = null;
  private $player = null;

  private $media_attachments = array();

  private function processData(Base $feedbase, $meta_key, SimpleXMLElement $meta_value)
  {
    switch ((string)$meta_key)
    {
      case 'title':
      case 'keywords':
      case 'player':
        $this->$meta_key = (string)$meta_value;
        break;
      case 'thumbnail':
        $this->$meta_key = (string)$meta_value['url'];
        break;
      case 'content':
        $media_content = array();
        foreach ($meta_value->attributes() as $sub_meta_key => $sub_meta_value)
        {
          $media_content[$sub_meta_key] = (string)$sub_meta_value;
        }
        if (isset($media_content['url']))
        {
          $this->media_attachments[sha1($media_content['url'])] = $media_content;
        }
        break;
    }
  }

  public function applyMetaData(Base $feedbase)
  {
    if (isset($this->title) && !isset($feedbase->title))
    {
      $feedbase->media['title'] = $this->title;
    }
    if (isset($this->thumbnail))
    {
      $feedbase->media['thumbnail'] = $this->thumbnail;
    }
    if (isset($this->keywords))
    {
      $feedbase->media['keywords'] = $this->keywords;
    }
    if (isset($this->player))
    {
      $feedbase->media['player'] = $this->player;
    }
    if (count($this->media_attachments) > 0)
    {
      $feedbase->media['group'] = $this->media_attachments;
    }
  }

  public function processMetaData(Base $feedbase, $meta_namespace, $meta_key, SimpleXMLElement $meta_value)
  {
    if ($feedbase instanceof Item)
    {
      switch ((string)$meta_namespace)
      {
        case 'media':
          switch ((string)$meta_key)
          {
            case 'group':
              if (count($meta_value->children($meta_namespace, true)) > 0)
              {
                foreach ($meta_value->children($meta_namespace, true) as $sub_meta_key => $sub_meta_value)
                {
                  $this->processMetaData($feedbase, $meta_namespace, $sub_meta_key, $sub_meta_value);
                }
              }
              break;

            default:
              $this->processData($feedbase, $meta_key, $meta_value);
              break;
          }
          break;
      }
    }
    unset($meta_namespace, $meta_key, $meta_value);
  }
}
