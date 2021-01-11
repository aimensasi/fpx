<?php

namespace Aimensasi\FPX\Commands;

use Illuminate\Console\Command;

class UpdateBankList extends Command {
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'fpx:banks';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Update FPX banks List.';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return int
	 */
	public function handle() {
		$result = (new UpdateBankList)->handle();

		if ($result) {
			$this->info("Bank list updated successfully");
		} else {
			$this->info("Bank list updated successfully");
		}
	}
}
