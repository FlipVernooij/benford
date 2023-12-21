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
                "threshold" => "Threshold to use for deviation (optional, default .15)"
            ]
        ], options: JSON_PRETTY_PRINT);
    }

    public function postBenford( Request $request): JsonResponse
    {
        // @todo Using a html form data now, I would prefer json but that involves adding javascript to frontend....
        $post=$request->post();
        $params = [
            "test_set" => json_decode($post["test_set"])
        ];
        if($params["test_set"] === null){
            return response()->json(["status" => false, "error" => "Faulty test_set"], status: 422); // Unprocessable entity.
        }
        foreach($params["test_set"] as $value){
            if(is_numeric($value) === false){
                return response()->json(["status" => false, "error" => "Faulty test_set"], status: 422); // Unprocessable entity.
            }
        }
        if(isset($post['threshold'])){
            $params["threshold"] = (float) $post["threshold"];
        }
        return response()->json(FraudDetection::isBenford(...$params), options: JSON_PRETTY_PRINT);
    }
}
