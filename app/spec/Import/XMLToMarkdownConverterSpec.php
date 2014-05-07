<?php

namespace spec\Import;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class XMLToMarkdownConverterSpec extends ObjectBehavior
{
    function let($xml){
        $xml = new \SimpleXMlElement('<xml></xml>');

        $this->beConstructedWith($xml);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Import\XMLToMarkdownConverter');
    }

    function it_should_set_XML_Element(){
        $this->getXMLElement()->shouldReturnAnInstanceOf('SimpleXMlElement');
    }




}
