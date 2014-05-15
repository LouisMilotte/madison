<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Import\Downloader;

class DocDownload extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'doc:download';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Download a document for import.';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		$downloader = new Downloader();

		//If this is a bulk download
		if($this->option('bulk')){
			//Set the zip path and xml paths
			$zip_path = storage_path() . '/docs/zip/';
			$xml_path = storage_path() . '/docs/xml/';

			//Download the input filname for the zip file to the zip path
			$filename = $downloader->download_file($this->argument('doc'), $zip_path, true);

			//Unzip the zip path to the xml path
			$downloader->unzip_archive($zip_path . 'bulk.zip', $xml_path);
		} 
		//Default to single download
		else {
			
			//If neither option was supplied, issue a warning
			if(!$this->option('single')){
				$this->info("No 'single' / 'bulk' option was chosen.  Defaulting to 'single'");
			}

			//The single document storage path
			$path = storage_path() . '/docs/xml/';

			//Simply download the document and store it in the xml path
			$filename = $downloader->download_file($this->argument('doc'), $path);
		}
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			array('doc', InputArgument::REQUIRED, 'The document to download.')
		);
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array(
			array('single', null, InputOption::VALUE_NONE, 'Download a single document', null),
			array('bulk', null, InputOption::VALUE_NONE, 'Download documents in bulk ( as a .zip )')
		);
	}

}
