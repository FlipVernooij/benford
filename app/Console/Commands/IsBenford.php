<?php

namespace App\Console\Commands;

use App\Helpers\FraudDetection;
use Illuminate\Console\Command;

class IsBenford extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:is-benford {json_test_set} {--threshold=} {--adjust_for_big_set} ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifies if a set of numbers complies to Bendords law.';

    /**
     * Execute the console command.
     */
    public function handle() : int
    {
        $params= [
            "test_set" => json_decode($this->argument("json_test_set")),
            "adjust_for_big_set" => $this->option("adjust_for_big_set")
        ];
        if(!is_null($this->option("threshold"))){
            $params["threshold"] = (float) $this->option("threshold"); # @todo unvalidated float cast
        }
        $response = FraudDetection::isBenford(...$params);
        echo $response;
        return 1; // is this correct, assuming it is the return code.
    }
}
