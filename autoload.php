<?php

/**
 * projectname: metaverse/feedparser
 *
 * @package metaverse/feedparser
 */

/**
 * my little autoloader function
 *
 * @param $className
 */
function __autoload($className)
{
  $className = ltrim($className, '\\');
  $fileName = dirname(__FILE__) . DIRECTORY_SEPARATOR;
  $namespace = '';
  if ($lastNsPos = strrpos($className, '\\')) {
    $namespace = substr($className, 0, $lastNsPos);
    $className = substr($className, $lastNsPos + 1);
    $fileName .= str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
  }
  $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';

  if (file_exists($fileName)) {
    require_once $fileName;
  }
}
