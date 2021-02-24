<?php

namespace App\Http\Controllers;

use Auth;
use App\User;
use App\Payment;
use App\Streamers;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use GuzzleHttp\Client;
use Carbon\Carbon;

class StreamerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except('home');
    }

    public function home($username)
    {
        $stream = User::whereRaw('LOWER(username) = "' . strtolower($username) . '"')->has('stream')->firstOrFail();
        return view('pay', compact('stream'));
    }

    public function register()
    {
        $stream = Streamers::where('user_id', auth()->id())->get()->first();
        return view('register', compact('stream'));
    }

    public function new(Request $request)
    {
        if ($request->filled(['picpay_token', 'seller_token'])) {
            $streamer = Streamers::updateOrCreate([
                'user_id' => auth()->id()
            ], [
                'picpay_token' => $request->picpay_token,
                'seller_token' => $request->seller_token
            ]);
            if (!$streamer->token) {
                $streamer->token = Str::orderedUuid();
            }
            if ($request->filled('se_jwt')) {
                $streamer->se_jwt = $request->se_jwt;
            }
            $streamer->save();

            return response()->json($streamer);
        }
        return response()->json(['error' => true]);
    }

    public function test(Request $request)
    {
        if ($request->filled(['se_jwt'])) {
            $client = new Client(['verify' => false]);
            $headers = [
                'Authorization' => 'Bearer ' . trim($request->se_jwt),
                'Accept' => 'application/json'
            ];
            $url = 'https://api.streamelements.com/kappa/v2/users/access';
            $response = $client->request('GET', $url, ['headers' => $headers, 'decode_content' => false]);
            if ($response->getStatusCode() < 200 || $response->getStatusCode() >= 300) {
                return response()->json(['error' => true]);
            }
            $channel = json_decode($response->getBody()->getContents());
            $client = new Client(['verify' => false]);
            $url = 'https://api.streamelements.com/kappa/v2/tips/' . $channel[0]->channelId;
            $data = [
                "user" => [
                    "username" => "XogumTV",
                    "email" => "teste@xogum.tv"
                ],
                "provider" => "picpay - xogum",
                "message" => "Isso é apenas um teste!",
                "amount" => mt_rand(1, 100),
                "currency" => "BRL",
                "imported" => true
            ];
            $response = $client->request('POST', $url, ['headers' => $headers, 'decode_content' => false, 'json' => $data]);
            if ($response->getStatusCode() < 200 || $response->getStatusCode() >= 300) {
                return response()->json(['error' => true]);
            }
            $response = json_decode($response->getBody()->getContents());

            return response()->json($response);
        }
    }
}
