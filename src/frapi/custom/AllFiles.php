<?php

// Use the constant CUSTOM_MODEL to access the custom model directory
define ('COUCH_DB', CUSTOM_MODEL . DIRECTORY_SEPARATOR . 'CouchDB');

require CUSTOM_MODEL . DIRECTORY_SEPARATOR . 'CouchDB.php';
require COUCH_DB . DIRECTORY_SEPARATOR . 'Request.php';
require COUCH_DB . DIRECTORY_SEPARATOR . 'Response.php';

require CUSTOM_MODEL. DIRECTORY_SEPARATOR . 'MyThumbnailsDb.php';
require CUSTOM_MODEL. DIRECTORY_SEPARATOR . 'Thumbnails.php';
require CUSTOM_MODEL . '/Webthumb/Bluga/PEAR2/Autoload.php';
require CUSTOM_MODEL . '/Webthumb/Bluga/Autoload.php';
require CUSTOM_MODEL . '/Spaz/Thumb.php';
require CUSTOM_MODEL . '/Spaz/Urltitle.php';
require CUSTOM_MODEL . '/Spaz/Urlinfo.php';
require CUSTOM_MODEL . '/Frapi/Limit.php';

require CUSTOM_MODEL . '/AppSTW.php';

require dirname(__FILE__) . DIRECTORY_SEPARATOR.'helpers.php';
