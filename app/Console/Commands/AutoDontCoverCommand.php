<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use KisCore\Infrastructure\Singleton;
use Tookan\DefaultValues\TookanCountries;
use KisServices\TaskService;

class AutoDontCoverCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto_dont_cover';
    private $taskService;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->taskService = Singleton::Create(TaskService::class);
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        foreach(TookanCountries::$Values as $country => $object){
            $this->taskService->SendDontCoverMessageToFailedTasks($country);
        }
        return Command::SUCCESS;
    }
}
