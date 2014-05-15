<?php

namespace Import;

use \Exception;
use Import\Exceptions\IncorrectXMLFormatException;
use Import\Exceptions\IncorrectType;
use Import\Exceptions\FileNotFoundException;
use Import\Exceptions\IncorrectFileTypeException;

use Import\XMLToMarkdownConverter;

use \Doc;
use \DocContent;
use \Status;
use \DocMeta;

class Importer
{    

    public function __construct()
    {

    }

    public function importDirectory($directory)
    {
        //Set up return array
        $returnMessage = array(
            'success'   => array(
                                 'count' => 0,
                                 'results' => array()
                            ),
            'skipped'   => array(
                                 'count' => 0,
                                 'results' => array()
                            ),
            'error'     => array(
                                 'count' => 0,
                                 'results' => array()
                            )
        );

        //Grab all the filenames in the directory
        $filenames = $this->getDirectoryFiles($directory);

        //Make sure directory has a trailing slash
        if('/' !== substr($directory, -1)){
            $directory .= '/';
        }
        
        //Import each file
        foreach($filenames as $filename){
            $result = $this->importFile($directory . $filename);

            $returnMessage[$result['status']]['count']++;

            array_push($returnMessage[$result['status']]['results'], $result);
        }

        return $returnMessage;
    }

    public function getDirectoryFiles($directory){
        if(!file_exists($directory)){
          throw new FileNotFoundException($directory);
        }

        $filenames = scandir($directory);
        $filenames = array_diff($filenames, array('.', '..'));

        return $filenames;
    }

    public function importFile($file){
        $converter = new XMLToMarkdownConverter();

        $returnMessage = array();

        if(!file_exists($file)){
            throw new FileNotFoundException($file);
        }

        $xml = file_get_contents($file);
        $converter->setXML($xml);


        try{
            //Get document information
            $body = $converter->getBody();
            $title = $converter->getTitle();
            $slug = $converter->createSlug($title);
            $sponsor = $converter->getSponsor();
            $status = $converter->getStatus();
            $committee = $converter->getCommittee();

            //Create Doc
            $doc = Doc::where('slug', $slug)->first();

            //If this document already exists
            if(isset($doc)){
               $returnMessage['status'] = 'skipped';
               $returnMessage['message'] = 'Document with slug ' . $slug . ' already exists.';
               $returnMessage['id'] = $doc->id;
            }else{
                $doc = $this->saveNewDoc($title, $slug, $body);
                $this->saveSponsor($sponsor, $doc->id);
                $this->saveStatus($status, $doc);
                $this->saveCommittee($committee, $doc->id);

                $returnMessage['status'] = 'success';
                $returnMessage['message'] = 'Document with slug ' . $slug . ' saved successfully';
                $returnMessage['id'] = $doc->id;
            }
        }catch(Exception $e){
            $returnMessage['status'] = 'error';
            $returnMessage['message'] = $e->getMessage() . "\n Filename: " . $file;
            $returnMessage['id'] = null;
        }catch(Import\Exceptions\IncorrectXMLFormatException $e){
            $returnMessage['status'] = 'error';
            $returnMessage['message'] = $e->getMessage() . "\n Filename: " . $file;
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

    protected function saveNewDoc($title, $slug, $body){
        $doc = new Doc();
        if(strlen($title) > 254){
            $title = substr($title, 0, 254);
        }

        if(strlen($slug) > 254){
            $slug = substr($slug, 0, 254);
        }
        $doc->title = $title;
        $doc->slug = $slug;
        $doc->save();    

        $starter = new DocContent();
        $starter->doc_id = $doc->id;
        $starter->content = $body;
        $starter->save();

        $doc->init_section = $starter->id;
        $doc->save();

        return $doc;
    }

    protected function saveSponsor($sponsor, $doc_id){
        //Save Doc Sponsor as DocMeta
            if(isset($sponsor) && !preg_match('/^\s*$/', $sponsor)){
                $sponsorObject = new DocMeta();
                $sponsorObject->doc_id = $doc_id;
                $sponsorObject->meta_key = 'imported_sponsor';
                $sponsorObject->meta_value = $sponsor;
                return $sponsorObject->save();
            }
    }

    protected function saveStatus($status, $doc){

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
        if(isset($committee) && !preg_match('/^\s*$/', $committee)){
            $committeeObject = new DocMeta();
            $committeeObject->doc_id = $doc_id;
            $committeeObject->meta_key = 'imported_committee';
            $committeeObject->meta_value = $committee;
            return $committeeObject->save();
        }
    }
}
