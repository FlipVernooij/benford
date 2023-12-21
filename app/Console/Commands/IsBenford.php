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
    protected $signature = 'app:is-benford {json_test_set} {--threshold=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifies if a set of numbers complies to Benfords law.';

    /**
     * Execute the console command.
     */
    public function handle() : int
    {
        $params= [
            "test_set" => json_decode($this->argument("json_test_set"))
        ];
        if(!is_null($this->option("threshold"))){
            $params["threshold"] = (float) $this->option("threshold");
        }
        if($this->validate($params) === false){
            return 22; // == invalid argument.
        }
        $response = FraudDetection::isBenford(...$params);
        echo $response;
        if($response->isPureBenford === true){
            return 0;
        }else{
            return 1;
        }
    }

    protected function validate(array $params): bool{
        foreach($params["test_set"] as $value){
            if(is_numeric($value) === false){
                return false;
            }
        }
        if(is_float($params["threshold"] ?? 0.0) === false){
            return false;
        }
        return true;
    }
}
