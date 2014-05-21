<?php 
use Import\Importer;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class DocImport extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'doc:import';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Import a document';

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
		$importer = new Importer();

		if($this->option('all')){
			$path = $this->argument('path');

			$results = $importer->importDirectory($path);
			$this->comment('Status for bulk import directory: ' . $path);

			$this->info('Successfull imports: ' . $results['success']['count']);
			$this->comment('Skipped imports: ' . $results['skipped']['count']);
			$this->comment('Old imports: ' . $results['old_files']['count']);
			$this->error('Import errors: ' . $results['error']['count']);

			$this->comment('Logging full results for each category...');
			$this->logResults($results);
			$this->info('Complete.');
		}else{
			$returned = $importer->importFile($this->argument('path'));	
			$this->comment('Status for document id: ' . (string)$returned['id']);

			switch($returned['status']){
				case 'success':
					$this->info($returned['message']);
					break;
				case 'skipped':
					$this->comment($returned['message']);
					break;
				case 'error':
					$this->error($returned['message']);
					break;
			}
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
			array('path', InputArgument::REQUIRED, 'The document path to import.'),
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
			array('all', null, InputOption::VALUE_NONE, 'Indicates \'path\' argument is a directory.  All files in the directory will be imported.', null),
		);
	}

	protected function logResults($results){
		$date = date('c', strtotime('now'));
		$logPath = storage_path() . '/import_logs';
		$successFile = $logPath . '/' . $date . '-success.log';
		$skippedFile = $logPath . '/' . $date . '-skipped.log';
		$errorFile = $logPath . '/' . $date . '-error.log';
		$oldFile = $logPath . '/' . $date . '-old.log';

		if(!file_exists($logPath)){
			$this->comment('Creating log directory');
			mkdir($logPath);
		}

		$this->logSuccess($successFile, $results['success']);
		$this->logSkipped($skippedFile, $results['skipped']);;
		$this->logError($errorFile, $results['error']);
		$this->logOldFiles($oldFile, $results['old_files']);
	}

	protected function logSuccess($filename, $successes){
		$fp = fopen($filename, 'w+');
		fwrite($fp, "Success Count: " . $successes['count'] . "\n\n");
		foreach($successes['results'] as $success){
			fwrite($fp, "Id (" . $success['id'] . "): " . $success['message'] . "\n");
		}
		fclose($fp);
	}

	protected function logSkipped($filename, $skipped){
		if($skipped['count'] === 0){
			return;
		}

		$fp = fopen($filename, 'w+');
		fwrite($fp, "Skip Count: " . $skipped['count'] . "\n\n");
		foreach($skipped as $skip){
			fwrite($fp, print_r($skip, true));
		}
		fclose($fp);
	}

	protected function logError($filename, $errors){
		if($errors['count'] === 0){
			return;
		}

		$fp = fopen($filename, 'w+');
		fwrite($fp, "Error Count: " . $errors['count'] . "\n\n");
		foreach($errors as $error){
			fwrite($fp, print_r($error, true));
		}
		fclose($fp);
	}

	protected function logOldFiles($filename, $oldFiles){
		if($oldFiles['count'] === 0){
			return;
		}

		$fp = fopen($filename, 'w+');
		fwrite($fp, "Old Files Count: " . $oldFiles['count'] . "\n\n");
		foreach($oldFiles as $oldFile){
			fwrite($fp, print_r($oldFile, true));
		}
		fclose($fp);
	}

}
