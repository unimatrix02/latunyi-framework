<?php $config = array (
  'database' => 
  array (
    'username' => 'demo',
    'password' => 123456,
    'name' => 'framework',
    'default_object_namespace' => '\\Application\\Domain\\Entity',
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
    'merging' => false,
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