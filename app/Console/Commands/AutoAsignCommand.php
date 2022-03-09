<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use KisCore\Infrastructure\Singleton;
use KisServices\AutoAsignQueueService;

class AutoAsignCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto_assign';
    private $autoAsignQueueService;

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
        $this->autoAsignQueueService = Singleton::Create(AutoAsignQueueService::class);
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if(!$this->autoAsignQueueService->IsProcessing()){
            $this->autoAsignQueueService->Run();
        }
        return Command::SUCCESS;
    }
}
