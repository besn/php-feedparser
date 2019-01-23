Feed Parser
===========
A generic PHP FeedParser library.

# Very simple usage example

```
$feedparser = new \FeedParser\FeedParser();
$feed = $feedparser->parse(
    file_get_contents('http://feeds.bbci.co.uk/news/world/rss.xml')
);

if ($feed instanceof \FeedParser\Feed) {
    foreach ($feed->items as $feed_item)
    {
        echo $feed_item->getTitle() . PHP_EOL;
    }
}
```