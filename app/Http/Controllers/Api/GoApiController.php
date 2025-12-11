<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Info(
 *     title="UAS API - Stock Market & General Services",
 *     version="1.0.0",
 *     description="API for UAS Pengembangan Aplikasi Bisnis - Machine to Machine (M2M) / Host to Host (H2H) using Laravel Passport Client Credentials Grant",
 *     @OA\Contact(
 *         email="rifkikurniawan2233@gmail.com",
 *         name="Rifki Setiawan"
 *     )
 * )
 * 
 * @OA\Server(
 *     url="http://localhost:8000/api",
 *     description="Local Development Server"
 * )
 * 
 * @OA\SecurityScheme(
 *     type="http",
 *     securityScheme="bearerAuth",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */
class GoApiController extends Controller
{
    /**
     * @OA\Get(
     *     path="/weather",
     *     tags={"General Services"},
     *     summary="Get weather information",
     *     description="Get current weather information for a specific city",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="city",
     *         in="query",
     *         description="City name",
     *         required=true,
     *         @OA\Schema(type="string", example="Jakarta")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="city", type="string", example="Jakarta"),
     *                 @OA\Property(property="temperature", type="string", example="28°C"),
     *                 @OA\Property(property="condition", type="string", example="Sunny"),
     *                 @OA\Property(property="humidity", type="string", example="65%"),
     *                 @OA\Property(property="timestamp", type="string", example="2025-12-11T03:00:00+00:00")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validation error"),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function getWeather(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'city' => 'required|string|max:100'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'city' => $request->city,
                    'temperature' => '28°C',
                    'condition' => 'Sunny',
                    'humidity' => '65%',
                    'timestamp' => now()->toIso8601String()
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/currency",
     *     tags={"General Services"},
     *     summary="Get currency exchange rate",
     *     description="Get exchange rate between two currencies",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="from",
     *         in="query",
     *         description="Source currency code (3 characters)",
     *         required=true,
     *         @OA\Schema(type="string", example="USD")
     *     ),
     *     @OA\Parameter(
     *         name="to",
     *         in="query",
     *         description="Target currency code (3 characters)",
     *         required=true,
     *         @OA\Schema(type="string", example="IDR")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="from", type="string", example="USD"),
     *                 @OA\Property(property="to", type="string", example="IDR"),
     *                 @OA\Property(property="rate", type="number", example=15750.50),
     *                 @OA\Property(property="amount", type="integer", example=1),
     *                 @OA\Property(property="timestamp", type="string")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validation error"),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function getCurrency(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'from' => 'required|string|size:3',
                'to' => 'required|string|size:3'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'from' => strtoupper($request->from),
                    'to' => strtoupper($request->to),
                    'rate' => 15750.50,
                    'amount' => 1,
                    'timestamp' => now()->toIso8601String()
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/news",
     *     tags={"General Services"},
     *     summary="Get news articles",
     *     description="Get news articles by category",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="category",
     *         in="query",
     *         description="News category",
     *         required=false,
     *         @OA\Schema(type="string", example="technology", default="technology")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="category", type="string", example="technology"),
     *                 @OA\Property(property="total", type="integer", example=2),
     *                 @OA\Property(property="articles", type="array", @OA\Items(type="object")),
     *                 @OA\Property(property="timestamp", type="string")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function getNews(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'category' => 'sometimes|string|max:50'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $category = $request->get('category', 'technology');

            return response()->json([
                'success' => true,
                'data' => [
                    'category' => $category,
                    'total' => 2,
                    'articles' => [
                        [
                            'id' => 1,
                            'title' => 'Latest ' . ucfirst($category) . ' News 1',
                            'description' => 'This is a sample ' . $category . ' news article',
                            'date' => now()->subDay()->toDateString(),
                            'source' => 'API Server'
                        ],
                        [
                            'id' => 2,
                            'title' => 'Latest ' . ucfirst($category) . ' News 2',
                            'description' => 'Another sample ' . $category . ' news article',
                            'date' => now()->toDateString(),
                            'source' => 'API Server'
                        ]
                    ],
                    'timestamp' => now()->toIso8601String()
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/data",
     *     tags={"General Services"},
     *     summary="Post data payload",
     *     description="Submit data payload to the server",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"payload"},
     *             @OA\Property(property="payload", type="array", @OA\Items(type="object"), example={{"name": "test", "value": 123}})
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Data received successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Data received successfully"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validation error"),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function postData(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'payload' => 'required|array|min:1'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            return response()->json([
                'success' => true,
                'message' => 'Data received successfully',
                'data' => [
                    'id' => uniqid(),
                    'received_payload' => $request->payload,
                    'timestamp' => now()->toIso8601String()
                ]
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/stock/price",
     *     tags={"Stock Market"},
     *     summary="Get stock price",
     *     description="Get current stock price for IDX symbol",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="symbol",
     *         in="query",
     *         description="Stock symbol (e.g., BBCA, TLKM)",
     *         required=true,
     *         @OA\Schema(type="string", example="BBCA")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="symbol", type="string", example="BBCA"),
     *                 @OA\Property(property="price", type="integer", example=8750),
     *                 @OA\Property(property="change", type="integer", example=150),
     *                 @OA\Property(property="change_percent", type="number", example=1.74),
     *                 @OA\Property(property="volume", type="integer", example=15234000),
     *                 @OA\Property(property="last_updated", type="string")
     *             ),
     *             @OA\Property(property="timestamp", type="string")
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validation error"),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function getStockPrice(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'symbol' => 'required|string|max:10'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $symbol = strtoupper($request->symbol);

            $stockData = [
                'BBCA' => ['price' => 8750, 'change' => 150, 'change_percent' => 1.74, 'volume' => 15234000],
                'TLKM' => ['price' => 3850, 'change' => -50, 'change_percent' => -1.28, 'volume' => 45678000],
                'ASII' => ['price' => 5200, 'change' => 100, 'change_percent' => 1.96, 'volume' => 23456000],
                'BMRI' => ['price' => 6300, 'change' => 200, 'change_percent' => 3.28, 'volume' => 34567000],
                'BBRI' => ['price' => 4850, 'change' => 150, 'change_percent' => 3.19, 'volume' => 56789000],
            ];

            $data = $stockData[$symbol] ?? [
                'price' => rand(1000, 10000),
                'change' => rand(-500, 500),
                'change_percent' => round(rand(-1000, 1000) / 100, 2),
                'volume' => rand(1000000, 50000000)
            ];

            return response()->json([
                'success' => true,
                'data' => [
                    'symbol' => $symbol,
                    'price' => $data['price'],
                    'change' => $data['change'],
                    'change_percent' => $data['change_percent'],
                    'volume' => $data['volume'],
                    'last_updated' => now()->toIso8601String()
                ],
                'timestamp' => now()->toIso8601String()
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/stock/profile",
     *     tags={"Stock Market"},
     *     summary="Get stock company profile",
     *     description="Get company profile information for IDX stock",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="symbol",
     *         in="query",
     *         description="Stock symbol",
     *         required=true,
     *         @OA\Schema(type="string", example="TLKM")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="symbol", type="string"),
     *                 @OA\Property(property="company_name", type="string"),
     *                 @OA\Property(property="sector", type="string"),
     *                 @OA\Property(property="subsector", type="string"),
     *                 @OA\Property(property="listing_date", type="string"),
     *                 @OA\Property(property="shares", type="integer"),
     *                 @OA\Property(property="listing_board", type="string")
     *             ),
     *             @OA\Property(property="timestamp", type="string")
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validation error"),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function getStockProfile(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'symbol' => 'required|string|max:10'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $symbol = strtoupper($request->symbol);

            $profiles = [
                'BBCA' => [
                    'company_name' => 'Bank Central Asia Tbk',
                    'sector' => 'Finance',
                    'subsector' => 'Bank',
                    'listing_date' => '2000-05-31',
                    'shares' => 24325000000,
                    'listing_board' => 'Main Board'
                ],
                'TLKM' => [
                    'company_name' => 'Telkom Indonesia Tbk',
                    'sector' => 'Infrastructure',
                    'subsector' => 'Telecommunication',
                    'listing_date' => '1995-11-14',
                    'shares' => 100800000000,
                    'listing_board' => 'Main Board'
                ],
                'ASII' => [
                    'company_name' => 'Astra International Tbk',
                    'sector' => 'Miscellaneous Industry',
                    'subsector' => 'Automotive',
                    'listing_date' => '1990-04-04',
                    'shares' => 40483553140,
                    'listing_board' => 'Main Board'
                ],
            ];

            $profile = $profiles[$symbol] ?? [
                'company_name' => $symbol . ' Tbk',
                'sector' => 'Various Industry',
                'subsector' => 'Others',
                'listing_date' => '2000-01-01',
                'shares' => rand(1000000000, 50000000000),
                'listing_board' => 'Main Board'
            ];

            return response()->json([
                'success' => true,
                'data' => array_merge(['symbol' => $symbol], $profile),
                'timestamp' => now()->toIso8601String()
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/stock/historical",
     *     tags={"Stock Market"},
     *     summary="Get stock historical data",
     *     description="Get historical stock price data",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="symbol",
     *         in="query",
     *         description="Stock symbol",
     *         required=true,
     *         @OA\Schema(type="string", example="ASII")
     *     ),
     *     @OA\Parameter(
     *         name="days",
     *         in="query",
     *         description="Number of days (1-30)",
     *         required=false,
     *         @OA\Schema(type="integer", example=7, default=7)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="symbol", type="string"),
     *                 @OA\Property(property="period_days", type="string"),
     *                 @OA\Property(property="historical", type="array", @OA\Items(type="object"))
     *             ),
     *             @OA\Property(property="timestamp", type="string")
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validation error"),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function getStockHistorical(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'symbol' => 'required|string|max:10',
                'days' => 'sometimes|integer|min:1|max:30'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $symbol = strtoupper($request->symbol);
            $days = $request->get('days', 7);

            $basePrice = rand(5000, 10000);
            $historical = [];

            for ($i = $days - 1; $i >= 0; $i--) {
                $variation = rand(-300, 300);
                $open = $basePrice + rand(-200, 200);
                $close = $basePrice + $variation;
                $high = max($open, $close) + rand(0, 200);
                $low = min($open, $close) - rand(0, 200);

                $historical[] = [
                    'date' => now()->subDays($i)->toDateString(),
                    'open' => $open,
                    'high' => $high,
                    'low' => $low,
                    'close' => $close,
                    'volume' => rand(10000000, 50000000)
                ];
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'symbol' => $symbol,
                    'period_days' => $days,
                    'historical' => $historical
                ],
                'timestamp' => now()->toIso8601String()
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/stock/movers",
     *     tags={"Stock Market"},
     *     summary="Get top stock movers",
     *     description="Get top gainers or losers in stock market",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="type",
     *         in="query",
     *         description="Type of movers (gainer or loser)",
     *         required=false,
     *         @OA\Schema(type="string", enum={"gainer", "loser"}, example="gainer", default="gainer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="type", type="string"),
     *                 @OA\Property(property="total", type="integer"),
     *                 @OA\Property(property="movers", type="array", @OA\Items(type="object"))
     *             ),
     *             @OA\Property(property="timestamp", type="string")
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validation error"),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function getStockMovers(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'type' => 'sometimes|string|in:gainer,loser'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $type = $request->get('type', 'gainer');

            $gainers = [
                ['symbol' => 'BBCA', 'price' => 8750, 'change' => 250, 'change_percent' => 2.94, 'volume' => 15234000],
                ['symbol' => 'TLKM', 'price' => 3900, 'change' => 200, 'change_percent' => 5.41, 'volume' => 45678000],
                ['symbol' => 'ASII', 'price' => 5200, 'change' => 180, 'change_percent' => 3.58, 'volume' => 23456000],
                ['symbol' => 'BMRI', 'price' => 6300, 'change' => 200, 'change_percent' => 3.28, 'volume' => 34567000],
                ['symbol' => 'BBRI', 'price' => 4850, 'change' => 150, 'change_percent' => 3.19, 'volume' => 56789000]
            ];

            $losers = [
                ['symbol' => 'UNVR', 'price' => 4200, 'change' => -180, 'change_percent' => -4.11, 'volume' => 12345000],
                ['symbol' => 'ICBP', 'price' => 8900, 'change' => -200, 'change_percent' => -2.20, 'volume' => 23456000],
                ['symbol' => 'INDF', 'price' => 6500, 'change' => -150, 'change_percent' => -2.26, 'volume' => 34567000],
                ['symbol' => 'KLBF', 'price' => 1580, 'change' => -40, 'change_percent' => -2.47, 'volume' => 45678000],
                ['symbol' => 'PTPP', 'price' => 1020, 'change' => -30, 'change_percent' => -2.86, 'volume' => 56789000]
            ];

            $movers = $type === 'gainer' ? $gainers : $losers;

            return response()->json([
                'success' => true,
                'data' => [
                    'type' => $type,
                    'total' => count($movers),
                    'movers' => $movers
                ],
                'timestamp' => now()->toIso8601String()
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server error',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
