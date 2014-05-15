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
        $returnMessage = array();

        if(!file_exists($file)){
            throw new FileNotFoundException($file);
        }

        $xml = file_get_contents($file);
        $this->converter->setXML($xml);

        $body = $this->converter->getBody();
        $title = $this->converter->getTitle();
        $slug = $this->converter->createSlug($title);
        $sponsor = $this->converter->getSponsor();
        $status = $this->converter->getStatus();
        $committee = $this->converter->getCommittee();

        //Create Doc
        try{
            $doc = Doc::where('slug', $slug)->first();

            //If this document already exists
            if(isset($doc)){
               $returnMessage['status'] = 'skipped';
               $returnMessage['message'] = 'Document with slug ' . $slug . ' already exists.';
               $returnMessage['id'] = $doc->id;
            }else{
                $doc_id = $this->saveNewDoc($title, $slug, $body);
                $this->saveSponsor($sponsor, $doc_id);
                $this->saveStatus($status, $doc_id);
                $this->saveCommittee($committee, $doc_id);

                $returnMessage['status'] = 'success';
                $returnMessage['message'] = 'Document with slug ' . $slug . ' saved successfully';
                $returnMessage['id'] = $doc_id;
            }
        }catch(Exception $e){
            $returnMessage['status'] = 'error';
            $returnMessage['message'] = $e->getMessage();
            $returnMessage['id'] = null;
        }
        
        return $returnMessage;
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

    protected function saveDoc($title, $slug, $body){
        $doc = new Doc();
        $doc->title = $title;
        $doc->slug = $slug;
        $doc->save();

        $starter = new DocContent();
        $starter->doc_id = $doc->id;
        $starter->content = $body;
        $starter->save();

        $doc->init_section = $starter->id;
        $doc->save();

        return $doc->id;
    }

    protected function saveSponsor($sponsor, $doc_id){
        //Save Doc Sponsor as DocMeta
            if(isset($sponsor)){
                $sponsorObject = new DocMeta();
                $sponsorObject->doc_id = $doc_id;
                $sponsorObject->meta_key = 'imported_sponsor';
                $sponsorObject->meta_value = $sponsor;
                return $sponsorObject->save();
            }
    }

    protected function saveStatus($status, $doc_id){
        //Save Doc Status
        if(isset($status)){
            $statusObject = Status::where('label', $status)->first();

            if(!isset($statusObject)){
                $statusObject = new Status();
                $statusObject->label = $status;
            }

            $statusObject->save();
            return $doc->statuses()->sync(array($statusObject->id));
        }
    }

    protected function saveCommittee($committee, $doc_id){
        //Save Doc Committee
        if(isset($committee)){
            $committeeObject = new DocMeta();
            $committeeObject->doc_id = $doc->id;
            $committeeObject->meta_key = 'imported_committee';
            $committeeObject->meta_value = $committee;
            return $committeeObject->save();
        }
    }
}
