<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class DatabaseBackup extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'db:backup';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Backs up the current database with timestamp.';

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
		$backups_path = storage_path() . '/db_backups';

		if(!file_exists($backups_path)){
			$this->info('Backups directory doesn\nt exist.  Creating...');
			mkdir($backups_path);
		}

		$creds = yaml_parse_file(app_path() . '/config/creds.yml');
		$timestamp = date('c', strtotime('now'));
		$filename = $timestamp . '_bak.sql';

		exec('mysqldump ' . $creds['database'] . ' -u' . $creds['username'] . ' -p' . $creds['password'] . ' > ' . $backups_path . '/' . $filename);

		$this->info("Backup $filename created.");
	}
}
