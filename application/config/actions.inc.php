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
);