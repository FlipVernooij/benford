<?php

namespace App\Http\Controllers\Api;

use App\Helpers\FraudDetection;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FraudController extends Controller
{
    public function getBenford(): JsonResponse
    {
        return response()->json([
            'required_request_method' => "post",
            'required_params' => [
                "test_set" => "numeric array on integers to verify (required)",
                "threshold" => "Threshold to use for deviation (optional, default .15)",
                "adjust_for_big_set" => "When set, we slightly change the calculation to favor big data-sets (optional, default false"
            ]
        ], options: JSON_PRETTY_PRINT);
    }

    public function postBenford( Request $request): JsonResponse
    {
        // @todo Using a html form data now, I would prefer json.
        $post=$request->post();
        $params = ["test_set" => json_decode($post["test_set"])]; // @todo skipped validation
        if(isset($post['threshold'])){
            $params["threshold"] = (float) $post["threshold"];
        }
        if($post["adjust_for_big_set"] ?? 'no' === 'yes'){
            $params["adjust_for_big_set"] = true;
        }else{
            $params["adjust_for_big_set"] = false;
        }
        return response()->json(FraudDetection::isBenford(...$params), options: JSON_PRETTY_PRINT);
    }
}
