<?php $config = array (
  '/' => 
  array (
    'controller' => 'Test',
    'method' => 'showList',
    'vars' => 
    array (
      'content' => 'list.php',
    ),
    'files' => 
    array (
      'stylesheets' => 
      array (
        0 => 'extra.css',
      ),
      'scripts' => 
      array (
        0 => 'test.js',
      ),
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
      'scripts' => 
      array (
        0 => 'script.js',
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