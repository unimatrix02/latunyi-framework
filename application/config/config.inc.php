<?php $config = array (
  'database' => 
  array (
    'username' => 'root',
    'password' => 'Di19Mp78!',
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
      1 => 'style.css',
    ),
    'scripts' => 
    array (
      0 => 'jquery.js',
    ),
  ),
);