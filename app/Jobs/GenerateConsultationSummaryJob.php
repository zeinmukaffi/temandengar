<?php

namespace App\Jobs;

use App\Models\Consultation;
use App\Services\ConsultationSummaryService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class GenerateConsultationSummaryJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 1;
    public int $timeout = 210;

    public function __construct(
        public int $consultationId
    ) {
    }

    public function handle(ConsultationSummaryService $service): void
    {
        $consultation = Consultation::find($this->consultationId);

        if (!$consultation) {
            return;
        }

        $service->generate($consultation);
    }
}