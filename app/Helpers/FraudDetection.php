<?php

namespace App\Helpers;

/**
 * Response object for the Benford test.
 * Allows for additional Benford variants like double digits or adjusted distributions in the future.
 *
 */
class BenfordResponse{
    public bool $expectedFraud = false;
    public bool $isPureBenford = false;
    public int $setCount = 0;
    public float $deviance = 0.0;
    public float $threshold = 0.0;
    public array $counts= [];
    public array $percentages= [];
    public array $benfordsDistribution = [];


    public function __toString():string{
        return json_encode(get_object_vars($this), JSON_PRETTY_PRINT);
    }
}
class FraudDetection
{
    /**
     * @param array $digit_set Array of integers representing the test-set.
     * @param float $threshold Set to a reasonable .15
     *
     * @return BenfordResponse
     *
     * @todo While getting close to the suggested benford sequence as stated on wikipedia, it is not precise.
     *       This most probably has to do with rounding errors, leaving this for now.
     *       I could just hardcode the sequence.
     */
    static public function isBenford(array $test_set, float $threshold = .15):BenfordResponse{
        $response = new BenfordResponse();
        $response->threshold = $threshold;
        $response->setCount = count($test_set);

        # first, get first digit of every provided number
        $response->counts = array_fill(1,9, 0);

        # loop over every array entry, normalise it and append the first digit to the $digitFirsts "assoc-array"
        foreach($test_set as $value){
            $tmp = (int) substr((string) abs($value), 0, 1);
            $response->counts[$tmp]++;
        }

        # loop over each single digit and calculate the following
        #  1.) Benfords expected percentage for the digit
        #  2.) The actual percentage of occurance for the digit.
        #  3.) The deviance of expected vs. actual
        for($i=1;$i<=9;$i++){
            $response->benfordsDistribution[$i] = round((log10($i + 1) - log10($i)) * 100, 2);
            $response->percentages[$i] = round(($response->counts[$i] / $response->setCount) * 100, 2);
            $response->deviance += pow($response->percentages[$i] - $response->benfordsDistribution[$i], 2) / $response->benfordsDistribution[$i];
        }
        # set/round totals
        $response->deviance = round($response->deviance, 2);
        $response->isPureBenford = $response->deviance < $response->threshold;
        $response->expectedFraud = !$response->isPureBenford;
        return $response;
    }
}
