<?php

require_once(dirname(__FILE__) . '/autoload.php');

if (array_key_exists('url', $_REQUEST) && !empty($_REQUEST['url']))
{
  try
  {
    $feed_content = file_get_contents($_REQUEST['url']);
    if (!empty($feed_content))
    {
      $feedparser = new \FeedParser\FeedParser();
      $feed = $feedparser->parse($feed_content);
    }
  }
  catch (Exception $ex)
  {
    echo $ex->getMessage();
  }
}
?>
<html>
  <head>
    <title>FeedParser Example</title>
  </head>
  <body>
    <form>
      RSS/RDF/Atom-Feed URL: <input type="url" name="url" placeholder=""><button>go</button>
    </form>

<?php
if (isset($feed) && $feed instanceof \FeedParser\Feed) {
?>
    <hr>
    <h1><?php echo $feed->getTitle(); ?></h1>
<?php
  if (count($feed->items) > 0)
  {
?>
    <ul>
<?php
    /** @var \FeedParser\Item $feed_item */
    foreach ($feed->items as $feed_item)
    {
?>
      <li>
        <a href="<?php echo $feed_item->getLink(); ?>"><?php echo $feed_item->getTitle(); ?></a>
<?php if (!empty($feed_item->getDescription())) { echo '<br>' . $feed_item->getDescription(); } ?>
      </li>
<?php
    }
?>
    </ul>
<?php
  }
}
?>
  </body>
</html>
