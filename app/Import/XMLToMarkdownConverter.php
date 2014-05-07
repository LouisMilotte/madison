<?php

namespace Import;

use Import\Exceptions\IncorrectArgumentCountException;
use Import\Exceptions\FileNotFoundException;
use SimpleXMLElement;

class XMLToMarkdownConverter
{
    protected $xml;

    public function __construct(SimpleXMLElement $xml){
        if(!isset($xml)){
            throw new IncorrectArgumentCountException();
        }

        $this->xml = $xml;
    }

    public function getXMLElement(){
        return $this->xml;
    }
}
