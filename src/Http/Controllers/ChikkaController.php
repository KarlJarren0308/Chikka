<?php

namespace KarlMacz\Chikka\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

use KarlMacz\Chikka\Models\ChikkaIncomingSmsModel;
use KarlMacz\Chikka\Models\ChikkaOutgoingSmsModel;

use Log;

class ChikkaController extends Controller
{
    const CHIKKA_URI = 'https://post.chikka.com/smsapi/request';
    private $access_code;
    private $client_id;
    private $secret_key;
    private $request_cost;

    public function readIncomingSms($id = null) {
        if(config('chikka.store_transactions') === true) {
            return ($id === null ? ChikkaIncomingSmsModel::all() : ChikkaIncomingSmsModel::firstOrFail($id));
        } else {
            return 'Oops! Unable to read stored sms.';
        }
    }

    public function readOutgoingSms($id = null) {
        if(config('chikka.store_transactions') === true) {
            return ($id === null ? ChikkaOutgoingSmsModel::all() : ChikkaOutgoingSmsModel::firstOrFail($id));
        } else {
            return 'Oops! Unable to read stored sms.';
        }
    }

    public function receive(Request $request) {
        try {
            $message_type = $request->input('message_type');
        } catch(Exception $ex) {
            echo 'Error';

            return;
        }

        if(strtoupper($message_type) === 'INCOMING') {
            try {
                if(config('chikka.store_transactions') === true) {
                    ChikkaIncomingSmsModel::insert([
                        'mobile_number' => $request->input('mobile_number'),
                        'request_id' => $request->input('request_id'),
                        'message' => $request->input('message'),
                        'timestamp' => $request->input('timestamp')
                    ]);
                }

                echo 'Accepted';
            } catch (Exception $ex) {
                echo 'Error';
            }
        } else {
            echo 'Error';
        }
    }

    public function send(Request $request) {
        $message_type = strtoupper($request->input('message_type'));
        $mobile_number = $request->input('mobile_number');
        $message = $request->input('message');
        $message_id = $this->generateMessageID();
        $response = null;

        $client = new Client([
            'redirect.disable' => true
        ]);

        switch($message_type) {
            case 'SEND':
                try {
                    $http = $client->request('POST', static::CHIKKA_URI, [
                        'form_params' => [
                            'message_type' => 'SEND',
                            'mobile_number' => $mobile_number,
                            'shortcode' => config('chikka.access_code'),
                            'message_id' => $message_id,
                            'message' => $message,
                            'client_id' => config('chikka.client_id'),
                            'secret_key' => config('chikka.secret_key')
                        ]
                    ]);

                    if($http->getStatusCode() === 200) {
                        if(config('chikka.store_transactions') === true) {
                            ChikkaOutgoingSmsModel::insert([
                                'message_type' => 'SEND',
                                'mobile_number' => $mobile_number,
                                'message_id' => $message_id,
                                'message' => $message,
                                'timestamp' => date('Y-m-d H:i:s')
                            ]);
                        }

                        $response = [
                            'status' => 'Success',
                            'message' => 'Message sent.'
                        ];
                    } else {
                        $response = [
                            'status' => 'Failed',
                            'message' => 'Message sending failed.'
                        ];
                    }
                } catch(RequestException $ex) {
                    $response = [
                        'status' => 'Failed',
                        'message' => 'Message sending failed. Catched Exception: RequestException.'
                    ];
                }

                break;
            case 'REPLY':
                try {
                    $request_id = $request->input('request_id');

                    $http = $client->request('POST', static::CHIKKA_URI, [
                        'form_params' => [
                            'message_type' => 'REPLY',
                            'mobile_number' => $mobile_number,
                            'shortcode' => config('chikka.access_code'),
                            'request_id' => $request_id,
                            'message_id' => $message_id,
                            'message' => $message,
                            'request_cost' => strtoupper(config('chikka.request_cost')),
                            'client_id' => config('chikka.client_id'),
                            'secret_key' => config('chikka.secret_key')
                        ]
                    ]);

                    if($http->getStatusCode() === 200) {
                        if(config('chikka.store_transactions') === true) {
                            ChikkaOutgoingSmsModel::insert([
                                'message_type' => 'REPLY',
                                'mobile_number' => $mobile_number,
                                'request_id' => $request_id,
                                'message_id' => $message_id,
                                'message' => $message,
                                'request_cost' => strtoupper(config('chikka.request_cost')),
                                'timestamp' => date('Y-m-d H:i:s')
                            ]);
                        }

                        $response = [
                            'status' => 'Success',
                            'message' => 'Message sent.'
                        ];
                    }
                } catch(RequestException $ex) {
                    $response = [
                        'status' => 'Failed',
                        'message' => 'Message sending failed.'
                    ];
                }

                break;
            default:
                $response = [
                    'status' => 'Failed',
                    'message' => 'Invalid message type.'
                ];

                break;
        }

        if($request->ajax()) {
            return response()->json($response);
        } else {
            $params = [
                'flash_status' => $response['status'],
                'flash_message' => $response['message']
            ];

            return back()->with($params);
        }
    }

    private function generateMessageID() {
        return date('YmdHis') . sprintf('%03d', mt_rand(0, 999)) . sprintf('%03d', mt_rand(0, 999)) . sprintf('%03d', mt_rand(0, 999)) . sprintf('%03d', mt_rand(0, 999)) . sprintf('%03d', mt_rand(0, 999)) . sprintf('%03d', mt_rand(0, 999));
    }
}
