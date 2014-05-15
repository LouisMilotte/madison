<?php

namespace spec\Import;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use PhpSpec\Util\Filesystem;

class DownloaderSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Import\Downloader');
    }

    function test_download_path_should_not_exist(){
      $path = dirname(__DIR__) . '/Import/downloader/';

      $this->file_exists($path)->shouldReturn(false);
    }

    function it_should_create_path_if_not_exists(){
      $url = 'http://www.gpo.gov/fdsys/bulkdata/BILLS/113/2/hr/BILLS-113hr1036eh.xml';
      $path = dirname(__DIR__) . '/Import/downloader/';
      $filename = 'BILLS-113hr1036eh.xml';

      //Attempt to download the file
      $this->download_File($url, $path);

      //Class wrapper for file_exists for testing purposed
      $this->file_exists($path)->shouldReturn(true);

      //Clean up
      $files = array_diff(scandir($path), array('..', '.'));
      foreach($files as $file){
        unlink($path . $file);
      }
      rmdir($path);
    }

    function it_should_download_a_file(){
      $url = 'http://www.gpo.gov/fdsys/bulkdata/BILLS/113/2/hr/BILLS-113hr1036eh.xml';
      $path = dirname(__DIR__) . '/Import/downloader/';
      $filename = 'BILLS-113hr1036eh.xml';
      
      $this->download_File($url, $path);

      $file_path = $path . $filename;

      $this->file_exists($file_path)->shouldReturn(true);

      //Clean up
      $files = array_diff(scandir($path), array('..', '.'));
      foreach($files as $file){
        unlink($path . $file);
      }
      rmdir($path);
    }

    // function the_download_log_should_not_exist(){
    //   $log_path = dirname(__DIR__) . '/Import/downloader/downloader.json';

    //   $this->file_exists($log_path)->shouldReturn(false);
    // }

    // function it_should_create_the_download_log_if_not_exists(){
    //   $log_path = dirname(__DIR__) . '/Import/downloader/downloader.json';
    //   $filename = 'testLogFilename.xml';

    //   $this->update_download_log($log_path, $filename);

    //   $this->file_exists($log_path)->shouldReturn(true);

    //   unlink($log_path);
    // }

    // function it_should_update_the_download_log(){
    //   $log_path = dirname(__DIR__) . '/Import/downloader/downloader.json';

    //   $filename = 'testLogFilename.xml';

    //   $this->update_download_log($log_path, $filename);

    //   $this->file_content
    // }
}
