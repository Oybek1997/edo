<?php

namespace App\Console\Commands;

use App\Http\Models\DocumentSigner;
use App\MailQueue;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendMail;
use App\Http\Models\Document;

class CancelDocument extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cancel:document';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send drip e-mails to a user';

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
     * @return int
     */
    public function handle()
    {
        ///------------
        $todayDate = date("Y-m-d");
        $earlierDate = date("Y-m-d", strtotime($todayDate . " -15 days"));
        $documents = Document::whereHas('documentSigners', function ($q) use ($earlierDate) {
            $q->where('action_type_id', '<>', 6)
                ->whereNotNull('taken_datetime')
                ->where('taken_datetime', '<', $earlierDate);
        })
            ->where('document_template_id', 636)
            ->whereIn('status', [1, 2])
            ->get();
        foreach ($documents as $key => $document) {
            $document->status = 6;
            $document->save();
        }
        echo "Document was cancelled. Count: ".count($documents);
        return 0;
    }
}
