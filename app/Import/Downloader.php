<?php

namespace Import;

use Import\Exceptions\IncorrectType;
use Import\Exceptions\FileNotFoundException;
use Import\Exceptions\IncorrectFileTypeException;

class Downloader
{

  const LOG_FILENAME = 'downloader.json';

  public function __construct(){
    
  }

  /**
  * CURL wrapper for downloading single document files
  *
  * Assumes filename is basename() of input $url
  */
  public function download_file($url, $path, $bulk = false) {
    if(!file_exists($path)){
      mkdir($path);
    }

    if($bulk === true){
      $filename = 'bulk.zip';
    }else{
      $filename = basename($url);  
    }
    

    $fp = fopen($path . "$filename", 'w+');
    $ch = curl_init(str_replace(" ", "%20", $url));
    curl_setopt($ch, CURLOPT_TIMEOUT, 50);
    curl_setopt($ch, CURLOPT_FILE, $fp);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_exec($ch);
    curl_close($ch);
    fclose($fp);

    return $filename;
  }

  public function unzip_archive($zip_path, $doc_path){
    $zip = new \ZipArchive;

    if($zip->open($zip_path) === true) {
      $zip->extractTo($doc_path);
      $zip->close();
    }else{
      throw new Exception('Unable to unzip ' . $path);
    }
  }

  public function update_download_log($log_path, $filename) {
    $pathinfo = pathinfo($log_path);

    if(!file_exists($pathinfo['dirname'])){
      mkdir($pathinfo['dirname']);
    }

    if(!file_exists($log_path)){
      $newLog = array('downloads' => array());
    }else{
      $newLog = json_decode(file_get_contents($log_path));
    }

    array_push($newLog['downloads'], $filename);

    file_put_contents($log_path, json_encode($newLog));
  }

  public function file_exists($path){
    return file_exists($path);
  }
}

