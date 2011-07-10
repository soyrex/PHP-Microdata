PHP MICRODATA
=============
This library allows you to easily parse microdata 
information from a remote HTML document - pretty 
simple really.

Simple Example
--------------
Instantiate the object and then return it to Json or a PHP
array...

    $md   = new MicrodataParser('http://example.url.here');
    $json = $md->getJson(); // Return JSON
    $arr  = $md->getArray(); // Return XML

