<?php $config = array (
  '/' => 
  array (
    'controller' => 'TestController',
    'method' => 'showList',
    'vars' => 
    array (
      'content' => 'list.php',
    ),
  ),
  '/item/(\\d+)/add' => 
  array (
    'controller' => 'TestController',
    'method' => 'showForm',
    'vars' => 
    array (
      'content' => 'form.php',
    ),
  ),
  '/item/(\\d+)/edit' => 
  array (
    'controller' => 'TestController',
    'method' => 'showForm',
    'vars' => 
    array (
      'content' => 'form.php',
    ),
  ),
  '/item/(\\d+)/remove' => 
  array (
    'controller' => 'TestController',
    'method' => 'removeItem',
    'vars' => 
    array (
      'content' => 'list.php',
    ),
  ),
  '/list' => 
  array (
    'controller' => 'TestController',
    'method' => 'showListAjax',
    'vars' => 
    array (
      'content' => 'list_ajax.php',
    ),
    'files' => 
    array (
      'styles' => 
      array (
        0 => 'fancybox/jquery.fancybox.css',
      ),
      'scripts' => 
      array (
        0 => 'jquery.fancybox.pack.js',
        1 => 'script.js',
      ),
    ),
  ),
  '/list/items' => 
  array (
    'controller' => 'TestController',
    'method' => 'showListItems',
    'template' => 'list_items.php',
  ),
  '/ajax/item/(\\d+)/add' => 
  array (
    'controller' => 'TestController',
    'method' => 'showAjaxForm',
    'template' => 'form_ajax.php',
  ),
  '/ajax/item/(\\d+)/edit' => 
  array (
    'controller' => 'TestController',
    'method' => 'showAjaxForm',
    'template' => 'form_ajax.php',
  ),
  '/ajax/item/(\\d+)/save' => 
  array (
    'controller' => 'TestControllerController',
    'method' => 'showAjaxForm',
    'template' => 'form_ajax.php',
  ),
  '/ajax/item/(\\d+)/remove' => 
  array (
    'controller' => 'TestControllerController',
    'method' => 'removeItemAjax',
    'output' => 'text',
  ),
);