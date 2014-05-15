<?php

namespace Import;

use Import\Exceptions\IncorrectType;
use Import\Exceptions\FileNotFoundException;
use Import\Exceptions\IncorrectFileTypeException;

use Import\XMLToMarkdownConverter;

class Importer
{    

    protected $converter;

    public function __construct()
    {
        $this->converter = new XMLToMarkdownConverter();
    }

    public function importDirectory($directory)
    {
        $filenames = $this->getDirectoryFiles($directory);

        foreach($filenames as $filename){

        }

        return $filenames;
    }

    public function getDirectoryFiles($directory){
        if(!file_exists($directory)){
          throw new FileNotFoundException($directory);
        }

        $filenames = scandir($directory);

        return $filenames;
    }

    public function importFile($file){
        if(!file_exists($file)){
            throw new FileNotFoundException($file);
        }

        $xml = file_get_contents($file);
        $this->converter->setXML($xml);

        $body = $this->converter->getBody();
        // $title = $this->converter->getTitle();
        // $slug = $this->converter->createSlug($title);
        // $sponsor = $this->converter->getSponsor();
        // $status = $this->converter->getStatus();
        // $committee = $this->converter->getCommittee();
        

        echo $body;
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

    public function getBodyAsMarkdown($xml){
        if(!isset($xml)){
            throw new IncorrectTypeException("XML not set");
        }

        $this->converter->setXML($xml);
        $markdown = $this->converter->getBody();

        return $markdown;
    }
}
