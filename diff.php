<?php
array (
  0 => 
  array (
    'key' => 'common',
    'old' => 
    (object) array(
       'setting1' => 'Value 1',
       'setting2' => 200,
       'setting3' => true,
       'setting6' => 
      (object) array(
         'key' => 'value',
         'doge' => 
        (object) array(
           'wow' => '',
        ),
      ),
    ),
    'actual' => 
    (object) array(
       'follow' => false,
       'setting1' => 'Value 1',
       'setting3' => NULL,
       'setting4' => 'blah blah',
       'setting5' => 
      (object) array(
         'key5' => 'value5',
      ),
       'setting6' => 
      (object) array(
         'key' => 'value',
         'ops' => 'vops',
         'doge' => 
        (object) array(
           'wow' => 'so much',
        ),
      ),
    ),
    'status' => 'changed',
  ),
  1 => 
  array (
    'key' => 'group1',
    'old' => 
    (object) array(
       'baz' => 'bas',
       'foo' => 'bar',
       'nest' => 
      (object) array(
         'key' => 'value',
      ),
    ),
    'actual' => 
    (object) array(
       'foo' => 'bar',
       'baz' => 'bars',
       'nest' => 'str',
    ),
    'status' => 'changed',
  ),
  2 => 
  array (
    'key' => 'group2',
    'old' => 
    (object) array(
       'abc' => 12345,
       'deep' => 
      (object) array(
         'id' => 45,
      ),
    ),
    'actual' => NULL,
    'status' => 'deleted',
  ),
  3 => 
  array (
    'key' => 'group3',
    'old' => NULL,
    'actual' => 
    (object) array(
       'deep' => 
      (object) array(
         'id' => 
        (object) array(
           'number' => 45,
        ),
      ),
       'fee' => 100500,
    ),
    'status' => 'added',
  ),
  4 => 
  array (
    'key' => 'sameNode',
    'old' => 
    (object) array(
       'setting1' => 'Value 1',
       'setting2' => 200,
       'setting3' => true,
       'setting6' => 
      (object) array(
         'key' => 'value',
         'doge' => 
        (object) array(
           'wow' => '',
        ),
      ),
    ),
    'actual' => 
    (object) array(
       'setting1' => 'Value 1',
       'setting2' => 200,
       'setting3' => true,
       'setting6' => 
      (object) array(
         'key' => 'value',
         'doge' => 
        (object) array(
           'wow' => '',
        ),
      ),
    ),
    'status' => 'changed',
  ),
)