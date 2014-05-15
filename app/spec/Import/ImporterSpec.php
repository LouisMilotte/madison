<?php

namespace spec\Import;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ImporterSpec extends ObjectBehavior
{

    function it_is_initializable()
    {   
        $this->shouldHaveType('Import\Importer');
    }

    function it_should_squawk_if_files_directory_doesnt_exist(){
        $this->shouldThrow('Import\Exceptions\FileNotFoundException')->during('getDirectoryFiles', array('foo'));
    }

    function it_should_get_list_of_downloaded_files(){
        $directory = dirname(__DIR__) . '/Import/testXMLFiles';

        $this->getDirectoryFiles($directory)->shouldBeArray();
    }

    function it_should_squawk_if_file_not_found(){
        $this->shouldThrow('Import\Exceptions\FileNotFoundException')->during('readContentsAsXML', array('foo.xml'));
    }

    function it_should_squawk_if_file_wrong_type(){
        $filename = $this->getRelativePathToFile('wrongType.txt');

        $this->shouldThrow('Import\Exceptions\IncorrectFileTypeException')->during('readContentsAsXML', array($filename));
    }

    function it_should_read_file_contents_into_simplexml(){
        $filename = $this->getRelativePathToFile('test1.xml');
        $this->readContentsAsXML($filename)->shouldReturnAnInstanceOf('SimpleXMLElement');
    }

    function getRelativePathToFile($filename){
        return dirname(__DIR__) . '/Import/testXMLFiles/' . $filename;
    }
}
