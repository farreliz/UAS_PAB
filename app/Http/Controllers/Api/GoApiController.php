<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GoApiController extends Controller
{
    // Endpoint 1: Get Weather
    public function getWeather(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'city' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Response dummy untuk testing
        return response()->json([
            'success' => true,
            'data' => [
                'city' => $request->city,
                'temperature' => '28°C',
                'condition' => 'Sunny',
                'humidity' => '65%'
            ]
        ], 200);
    }

    // Endpoint 2: Get Currency
    public function getCurrency(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'from' => 'required|string',
            'to' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Response dummy untuk testing
        return response()->json([
            'success' => true,
            'data' => [
                'from' => $request->from,
                'to' => $request->to,
                'rate' => 15750.50,
                'amount' => 1
            ]
        ], 200);
    }

    // Endpoint 3: Get News
    public function getNews(Request $request)
    {
        $category = $request->get('category', 'technology');

        // Response dummy untuk testing
        return response()->json([
            'success' => true,
            'data' => [
                'category' => $category,
                'articles' => [
                    [
                        'title' => 'Latest Tech News 1',
                        'description' => 'This is a sample tech news article',
                        'date' => now()->toDateString()
                    ],
                    [
                        'title' => 'Latest Tech News 2',
                        'description' => 'Another sample tech news article',
                        'date' => now()->toDateString()
                    ]
                ]
            ]
        ], 200);
    }

    // Endpoint 4: Post Data
    public function postData(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'payload' => 'required|array'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Response dummy untuk testing
        return response()->json([
            'success' => true,
            'message' => 'Data received successfully',
            'data' => $request->payload
        ], 201);
    }
}
