# Laravel Logger

Allows you to create logs in an arbitrary file, works with strings, arrays, objects.
Log-message supports the maximum output level limit.
When we want to print a big object, this will help us get a clean dumping data.

## Installation
Require this package in your composer.json and update composer. This will download the package.  

    composer require izusoft/log-message


## Usage
include the LogMessage class at top.

    use LogMessage;
    
    $file = 'file-name';
    $message = 'log message'; // string|array|object    
    $context = (object) [ // string|array|object default=null
       'level1' => [
           'level2' => [
               'level3' => [
                   'some' => 'context'
               ]
           ]
       ],
       'some' => 'context'
    ]; 
    $nesting = 2; // default = 5
    
    LogMessage::debug($file, $message, $context, $nesting);
    LogMessage::info($file, $message, $context, $nesting);
    LogMessage::notice($file, $message, $context, $nesting);
    LogMessage::warning($file, $message, $context, $nesting);
    LogMessage::error($file, $message, $context, $nesting);
    LogMessage::critical($file, $message, $context, $nesting);
    LogMessage::alert($file, $message, $context, $nesting);
    LogMessage::emergency($file, $message, $context, $nesting);
    
   
example result:

    [2020-04-26 16:48:45] file-name.DEBUG: log message
    ```
    stdClass Object
    (
        'level1' => Array
            (
                level2 => Array
                    *MAX LEVEL*
    
            )
    
        'some' => context
    )
    
    ```
