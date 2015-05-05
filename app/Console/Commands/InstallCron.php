<?php namespace App\Console\Commands;

use App\Http\Controllers\BackupController;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class InstallCron extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'cron:install';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Command description.';

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
		$this->info('Installing Laravel Cron');

		$cronfiles=exec('crontab -l',$output);

		foreach($output as $key=>$cron){
			if(strpos($cron,"artisan schedule:run")){
				unset($output[$key]);
			}
		}
		exec("crontab -r");

		$output[] = "*/1 * * * * php ".base_path()."/artisan schedule:run";

		$output = implode("\n",$output)."\n";

		$tmpFile = "/tmp/".uniqid()."_cron";
		$myFile = fopen($tmpFile, "w+");
		fwrite($myFile, $output);
		fclose($myFile);

		exec("crontab ".$tmpFile);

		$this->info('Done!');

	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return [
			//['example', InputArgument::REQUIRED, 'An example argument.'],
		];
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return [
			//['example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
		];
	}

}
