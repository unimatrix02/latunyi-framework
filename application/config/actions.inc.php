<?php $config = array (
  '/' => 
  array (
    'controller' => 'Test',
    'method' => 'showList',
    'vars' => 
    array (
      'content' => 'list.php',
    ),
  ),
  '/item/(\\d+)/add' => 
  array (
    'controller' => 'Test',
    'method' => 'showForm',
    'vars' => 
    array (
      'content' => 'form.php',
    ),
  ),
  '/item/(\\d+)/edit' => 
  array (
    'controller' => 'Test',
    'method' => 'showForm',
    'vars' => 
    array (
      'content' => 'form.php',
    ),
  ),
  '/item/(\\d+)/remove' => 
  array (
    'controller' => 'Test',
    'method' => 'removeItem',
    'vars' => 
    array (
      'content' => 'list.php',
    ),
  ),
  '/list' => 
  array (
    'controller' => 'Test',
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
    'controller' => 'Test',
    'method' => 'showListItems',
    'template' => 'list_items.php',
  ),
  '/ajax/item/(\\d+)/add' => 
  array (
    'controller' => 'Test',
    'method' => 'showAjaxForm',
    'template' => 'form_ajax.php',
  ),
  '/ajax/item/(\\d+)/edit' => 
  array (
    'controller' => 'Test',
    'method' => 'showAjaxForm',
    'template' => 'form_ajax.php',
  ),
  '/ajax/item/(\\d+)/save' => 
  array (
    'controller' => 'Test',
    'method' => 'showAjaxForm',
    'template' => 'form_ajax.php',
  ),
  '/ajax/item/(\\d+)/remove' => 
  array (
    'controller' => 'Test',
    'method' => 'removeItemAjax',
    'output' => 'text',
  ),
);