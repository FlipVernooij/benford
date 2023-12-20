<?php

namespace App\Helpers;

class BenfordResponse{
    public bool $isFraud = false;
    public bool $isBenford = false;

    public int $setCount = 0;
    public float $chiSquared = 0.0;

    public float $threshold = 0.0;

    public bool $adjustForBigSet = false;

    public array $counts= [];
    public array $percentiles= [];
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
     * @param bool $adjust_for_big_set Using big data-sets seem to favor the distribution as a percentage of the set rather than the fixed 100.
     *
     * @return BenfordResponse
     *
     * @todo While getting close to the suggested benford sequence as stated on wikipedia, it is not precise.
     *       This most probably has to do with rounding errors, leaving this for now.
     *
     * @todo I multiplied my Chi-squared by 100, assuming that is the right thing to do.. is it?
     */
    static public function isBenford(array $test_set, float $threshold = .15,  bool $adjust_for_big_set=false):BenfordResponse{
        $response = new BenfordResponse();
        $response->threshold = $threshold;
        $response->adjustForBigSet = $adjust_for_big_set;
        # first, get first digit of every provided number
        $response->setCount = count($test_set);
        $response->counts = array_fill(1,9, 0);

        # loop over every array entry, normalise it and append the first digit to the $digitFirsts "assoc-array"
        foreach($test_set as $value){
            $tmp = (int) substr((string) abs($value), 0, 1);
            $response->counts[$tmp]++;
        }

        for($i=1;$i<=9;$i++){
            if($response->adjustForBigSet === true){
                // @todo On some big sets the distribution seems to slighly change, I should account for that here...
                //$response->benfordsDistribution[$i] = log10(1 + 1 / $i) *  $response->setCount; // needs attention.
                $response->benfordsDistribution[$i] = round((log10($i + 1) - log10($i)) * 100, 2);
            }else{
                $response->benfordsDistribution[$i] = round((log10($i + 1) - log10($i)) * 100, 2);
            }

            $response->percentiles[$i] = round(($response->counts[$i] / $response->setCount) * 100, 2);
            $response->chiSquared += pow($response->percentiles[$i] - $response->benfordsDistribution[$i], 2) / $response->benfordsDistribution[$i];
        }
        $response->isFraud = $response->isBenford = ($response->chiSquared * 100) < $response->threshold;
        return $response;
    }
}
