<?php defined('SYSPATH') OR die('No direct access allowed.');
    $config['rss_title'] = 'Ushahidi RSS channel';
    $config['rss_description'] = 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.';
    
    $config['need_translate'] = true;
    // for translator plagin fields
    $config['to_translator_fields'] = array('incident_title'=>'title','incident_description'=>'text');
    $config['to_translator_lang'] = array('ru'=>'ru_RU','ua'=>'uk_UA');
    $config['original_lang'] = 'ru';
    $config['table_alias'] = 'i';
    // /for translator plagin fields 
    //0 - no items limit in channel
    $config['items_limit'] = 0;
    ?>