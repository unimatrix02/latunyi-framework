<?php $config = array (
  'modules' => 
  array (
    'site' => 
    array (
      'root_path' => '/',
      'controller_ns' => NULL,
      'template_path' => NULL,
      'secure' => false,
    ),
    'admin' => 
    array (
      'root_path' => '/admin',
      'controller_ns' => 'Admin',
      'template_path' => '/admin',
      'secure' => true,
      'username' => 'admin',
      'password' => 123456,
    ),
  ),
  'database' => 
  array (
    'username' => 'demo',
    'password' => 123456,
    'name' => 'framework',
  ),
  'log' => 
  array (
    'path' => 'application/log',
  ),
  'templating' => 
  array (
    'default' => 'page.php',
  ),
  'assets' => 
  array (
    'merging' => true,
    'autorefresh' => true,
    'minify' => true,
    'minify_commands' => 
    array (
      'styles' => 'cleancss -o [filename] [filename]',
      'scripts' => 'uglifyjs2 [filename] -o [filename]',
    ),
    'styles' => 
    array (
      0 => 'reset.css',
      1 => 'layout.css',
    ),
    'scripts' => 
    array (
      0 => '//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js',
    ),
  ),
);