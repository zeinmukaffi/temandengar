<?php

namespace App\Jobs;

use App\Models\Journal;
use App\Services\JournalReflectionService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class GenerateJournalReflectionJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 1;
    public int $timeout = 150;

    public function __construct(
        public int $journalId,
        public int $userId
    ) {
    }

    public function handle(JournalReflectionService $service): void
    {
        $journal = Journal::find($this->journalId);

        if (!$journal) {
            return;
        }

        $service->generate($journal, $this->userId);
    }
}