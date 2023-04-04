<?php

namespace App\Jobs;


use App\Exports\RefundExport;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RefundExportJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $filter;
    public $name;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($filter, $name)
    {
        $this->filter = $filter;
        $this->name = $name;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        (new RefundExport($this->filter))->store('public/refund_report_'.$this->name.'.xlsx');
    }
}
