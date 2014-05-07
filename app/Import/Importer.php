<?php

namespace Import;

use Import\Exceptions\IncorrectType;
use Import\Exceptions\FileNotFoundException;
use Import\Exceptions\IncorrectFileTypeException;

use Import\XMLToMarkdownConverter;

class Importer
{
    protected $url;

    public function __construct()
    {

    }

    public function getDownloadedFilenames($directory)
    {
        if(!file_exists($directory)){
          throw new FileNotFoundException($directory);
        }

        $filenames = scandir($directory);

        return $filenames;
    }

    public function readContentsAsXML($filename){
        if(!file_exists($filename)){
            throw new FileNotFoundException();
        }

        libxml_use_internal_errors(true);

        if(!$xml = simplexml_load_file($filename)){
            throw new IncorrectFileTypeException("Unable to parse XML file into SimpleXML: \n\n " . print_r(libxml_get_last_error(), true));
        }

        return $xml;
    }

    public function convertXML($xml){
        if(!isset($xml)){
            throw new IncorrectTypeException("XML not set");
        }

        $converter = new XMLToMarkdownConverter();
    }
}
