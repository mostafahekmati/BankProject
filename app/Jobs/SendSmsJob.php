<?php

namespace App\Jobs;

use App\Services\Sms\SMSService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendSmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $to;
    protected $message;

    /**
     * Create a new job instance.
     *
     * @param string $to
     * @param string $message
     */
    public function __construct(string $to, string $message)
    {
        $this->to = $to;
        $this->message = $message;
    }

    /**
     * Execute the job.
     *
     * @param SMSService $smsService
     * @return void
     */
    public function handle(SMSService $smsService): void
    {
        $smsService->send($this->to, $this->message);
    }
}
