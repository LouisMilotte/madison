<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class DatabaseClear extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'db:clear';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Clear database of all docs.';

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
		Eloquent::unguard();

		DB::statement('SET FOREIGN_KEY_CHECKS=0;');

		DocContent::truncate();
		AnnotationComment::truncate();
		AnnotationRange::truncate();
		AnnotationPermission::truncate();
		Annotation::truncate();
		Doc::truncate();

		DB::statement('SET FOREIGN_KEY_CHECKS=1;');
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(

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

		);
	}

}
