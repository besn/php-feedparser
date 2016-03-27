<?php

/**
 * @param $className
 */
function __autoload($className)
{
  $className = ltrim($className, '\\');
  $fileName = dirname(__FILE__) . DIRECTORY_SEPARATOR;
  if ($lastNsPos = strrpos($className, '\\'))
  {
    $namespace = substr($className, 0, $lastNsPos);
    $className = substr($className, $lastNsPos + 1);
    $fileName .= str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
  }
  $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';

  if (file_exists($fileName))
  {
    /** @noinspection PhpIncludeInspection */
    require_once $fileName;
  }
}
