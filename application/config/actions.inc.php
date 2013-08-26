<?php $config = array (
  '/' => 
  array (
    'controller' => 'Test',
    'method' => 'showHomepage',
    'output' => 'html',
    'vars' => 
    array (
      'content' => 'homepage.php',
    ),
    'files' => 
    array (
      'css' => 
      array (
        0 => 'layout.css',
      ),
      'js' => 
      array (
        0 => 'test.js',
      ),
    ),
  ),
  '/list' => 
  array (
    'controller' => 'Home',
    'method' => 'showList',
  ),
  '/list/(\\d+)/item/(\\d+)/view' => 
  array (
    'controller' => 'Home',
    'method' => 'viewItem',
    'render' => 'json',
  ),
  '/item/(\\d+)/view' => 
  array (
    'controller' => 'Home',
    'method' => 'showItem',
  ),
  '/item/(\\d+)/save' => 
  array (
    'controller' => 'Home',
    'method' => 'saveItem',
  ),
  '/item/new' => 
  array (
    'controller' => 'Home',
    'method' => 'showForm',
  ),
);