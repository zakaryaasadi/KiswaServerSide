<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use KisCore\Infrastructure\Singleton;
use KisServices\TaskService;

class AutoReviewCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto_review';
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
        $this->taskService->SendReviewMessageToSuccessTasks("UAE");
        $this->taskService->SendReviewMessageToSuccessTasks("KSA");
        $this->taskService->SendReviewMessageToSuccessTasks("KWT");
        $this->taskService->SendReviewMessageToSuccessTasks("OMN");
        $this->taskService->SendReviewMessageToSuccessTasks("BHR");
        return Command::SUCCESS;
    }
}
