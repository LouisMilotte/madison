<?php

namespace spec\Import;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class XMLToMarkdownConverterSpec extends ObjectBehavior
{

    function let(){
        $xmlpath = dirname(__FILE__) . '/testXMLFiles/test2.xml';
        $xml = file_get_contents($xmlpath);

        $this->setXML($xml);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Import\XMLToMarkdownConverter');
    }

    function it_should_get_the_body(){
        //Get test MD content
        $mdpath = dirname(__FILE__) . '/testXMLFiles/test2.md';
        $md = file_get_contents($mdpath);

        //Run test
        $this->getBody()->shouldReturn($md);
    }

    function it_should_get_the_title(){
        $this->getTitle()->shouldReturn('113 HR 163 RH: Sleeping Bear Dunes National Lakeshore Conservation and Recreation Act');
    }

    function it_should_get_the_slug(){
        //Title from test1.xml
        $title = '113 HR 163 RH: Sleeping Bear Dunes National Lakeshore Conservation and Recreation Act';
        
        //Run test
        $this->createslug($title)->shouldReturn('113-hr-163-rh-sleeping-bear-dunes-national-lakeshore-conservation-and-recreation-act');
    }

    function it_should_get_the_sponsor(){
        $this->getSponsor()->shouldReturn('Mr. Benishek');
    }

    function it_should_get_the_status(){
        $this->getStatus()->shouldReturn('Reported in House');
    }

    function it_should_get_the_committee(){
        $this->getCommittee()->shouldReturn('Committee on Natural Resources');
    }






}
