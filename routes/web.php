<?php

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    // $response = Http::withToken(env('WOLT_ACCESS_TOKEN'))->withBody(json_encode(
    //     [
    //         "pickup" => [
    //             "location" => [
    //                 "formatted_address" => "Arkadiankatu 3-6"
    //             ]
    //         ],
    //         "dropoff" => [
    //             "location" => [
    //                 "formatted_address" => "Otakaari 24, 02150 Espoo"
    //             ]
    //         ]
    //     ]
    // ), 'application/json')->post('https://daas-public-api.development.dev.woltapi.com/merchants/6364e0088018ce361efafca5/delivery-fee');
    return Inertia::render('Welcome', []);
});

Route::post('/delivery-fee', function (Request $request) {
    if ($request->search) {

        $response = Http::withToken(env('WOLT_ACCESS_TOKEN'))->withBody(json_encode(
            [
                "pickup" => [
                    "location" => [
                        "formatted_address" => $request->search['pickup']
                    ]
                ],
                "dropoff" => [
                    "location" => [
                        "formatted_address" => $request->search['dropoff']
                    ]
                ]
            ]
        ), 'application/json')->post('https://daas-public-api.development.dev.woltapi.com/merchants/6364e0088018ce361efafca5/delivery-fee');

        return $response->json();
    }
    return [];
})->name('delivery-fee');

Route::post('/delivery-order', function (Request $request) {

    if ($request->search) {
        $response = Http::withToken(env('WOLT_ACCESS_TOKEN'))->withBody(json_encode(
            [
                "pickup" => [
                    "location" => [
                        "formatted_address" => $request->search['pickup']
                    ],
                    "comment" => "The box is in front of the door",
                    "contact_details" => [
                        "name" => "John Doe",
                        "phone_number" => "+358 50 123 4567",
                        "send_tracking_link_sms" => false
                    ]
                ],
                "dropoff" => [
                    "location" => [
                        "formatted_address" => $request->search['dropoff']
                    ],
                    "contact_details" => [
                        "name" => "John Doe's Wife",
                        "phone_number" => "+358 50 123 4567",
                        "send_tracking_link_sms" => false
                    ],
                    "comment" => "Leave at the door, please"
                ],
                "customer_support" => [
                    "email" => "CS@wolt.com",
                    "phone_number" => "+358 50 123 4567",
                    "url" => "https://www.wolt.com"
                ],
                "merchant_order_reference_id" => null,
                "is_no_contact" => true,
                "contents" => [
                    [
                        "count" => 1,
                        "description" => "30W USB-C Power Adapter",
                        "identifier" => "12345",
                        "tags" => []
                    ]
                ],
                "tips" => [],
                "min_preparation_time_minutes" => 10,
                "scheduled_dropoff_time" => null,
            ]
        ), 'application/json')->post('https://daas-public-api.development.dev.woltapi.com/merchants/6364e0088018ce361efafca5/delivery-order');

        return $response->json();
    }
    return [];
})->name('delivery-order');



Route::get('/test', function () {
    $response = Http::withToken(env('WOLT_ACCESS_TOKEN'))->withBody(json_encode(
        [
            "pickup" => [
                "location" => [
                    "formatted_address" => "Arkadiankatu 3-6"
                ]
            ],
            "dropoff" => [
                "location" => [
                    "formatted_address" => "Otakaari 24, 02150 Espoo"
                ]
            ]
        ]
    ), 'application/json')->post('https://daas-public-api.development.dev.woltapi.com/merchants/6364e0088018ce361efafca5/delivery-fee');
});

// Route::get('/dashboard', function () {
//     $response = Http::withToken(env('WOLT_ACCESS_TOKEN'))->withBody(json_encode(
//         [
//             "pickup" => [
//                 "location" => [
//                     "formatted_address" => "Arkadiankatu 3-6"
//                 ]
//             ],
//             "dropoff" => [
//                 "location" => [
//                     "formatted_address" => "Otakaari 24, 02150 Espoo"
//                 ]
//             ]
//         ]
//     ), 'application/json')->post('https://daas-public-api.development.dev.woltapi.com/merchants/6364e0088018ce361efafca5/delivery-fee');
//     return Inertia::render(
//         'Dashboard',
//         [
//             'data' => $response->json()
//         ]
//     );
// })->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__ . '/auth.php';
