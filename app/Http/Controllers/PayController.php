<?php

namespace App\Http\Controllers;

use Auth;
use App\Payment;
use App\Streamers;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use GuzzleHttp\Client;
use Carbon\Carbon;

class PayController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except('callback');
    }

    public function history()
    {
        $payments = Payment::where('user_id', Auth::id())->get();
        return view('history', compact('payments'));
    }

    public function received()
    {
        $payments = Payment::where('to_user', Auth::id())->get();
        return view('received', compact('payments'));
    }

    public function payment($referenceId = NULL)
    {
        $payment = Payment::where('order_id', $referenceId)->where('user_id', Auth::id())->firstOrFail();
        return view('payment', compact('payment'));
    }

    public function callback()
    {
        $headers = array_change_key_case(getallheaders());
        file_put_contents('headers.txt', serialize($headers));
        if (isset($headers['x-seller-token'])) {
            $stream = Streamers::where('seller_token', $headers['x-seller-token'])->firstOrFail();
            $body = json_decode(file_get_contents('php://input'));
            $refId = $body->referenceId;
            $pay = Payment::where('order_id', $refId)->firstOrFail();
            $client = new Client(['verify' => false]);
            $headers = [
                'x-picpay-token' => $stream->picpay_token,
                'Accept' => 'application/json'
            ];
            $url = 'https://appws.picpay.com/ecommerce/public/payments/' . $refId . '/status';
            $response = $client->request('GET', $url, ['headers' => $headers, 'decode_content' => false]);
            if ($response->getStatusCode() < 200 || $response->getStatusCode() >= 300) {
                return response()->json(['error' => true]);
            }
            $response = json_decode($response->getBody()->getContents());
            if (isset($response->authorizationId)) {
                $pay->authorization_id = $response->authorizationId;
            }
            $pay->status = $response->status;
            $pay->save();
            if ($response->status === 'paid') {
                $client = new Client(['verify' => false]);
                $headers = [
                    'Authorization' => 'Bearer ' . trim($stream->se_jwt),
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
                        "username" => $pay->user->username,
                        "email" => $pay->user->email
                    ],
                    "provider" => "picpay - xogum",
                    "message" => $pay->message,
                    "amount" => $pay->amount,
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
            return response()->json(['updated' => true]);
        }
        return response()->json($headers);
    }

    public function pay(Request $request)
    {
        if ($request->filled(['streamer_id', 'name', 'surname', 'cpf', 'email', 'phone', 'amount'])) {
            $stream = Streamers::where('user_id', $request->streamer_id)->firstOrFail();
            $client = new Client(['verify' => false]);
            $headers = [
                'x-picpay-token' => $stream->picpay_token,
                'Accept' => 'application/json'
            ];
            $referenceId = Str::orderedUuid();
            $url = 'https://appws.picpay.com/ecommerce/public/payments';
            $data = [
                'referenceId' => $referenceId,
                'callbackUrl' => route('payment-callback'),
                'returnUrl' => route('payment-order', $referenceId),
                'value' => $request->amount,
                'expiresAt' => Carbon::now()->add(1, 'day')->toDateTimeString(),
                'buyer' => [
                    "firstName" => $request->name,
                    "lastName" => $request->surname,
                    "document" => $request->cpf,
                    "email" => $request->email,
                    "phone" => $request->phone
                ]
            ];
            $response = $client->request('POST', $url, ['headers' => $headers, 'json' => $data, 'decode_content' => false]);
            if ($response->getStatusCode() < 200 || $response->getStatusCode() >= 300) {
                return response()->json(['error' => true]);
            }
            $response = json_decode($response->getBody()->getContents());
            $pay = new Payment;
            $pay->user_id = auth()->id();
            $pay->to_user = $request->streamer_id;
            $pay->order_id = $referenceId;
            $pay->amount = $request->amount;
            if (isset($request->message)) {
                $pay->message = $request->message;
            }
            $pay->payment_url = $response->paymentUrl;
            $pay->expires_at = Carbon::parse($response->expiresAt);
            $pay->save();

            return response()->json($pay);
        }
        return response()->json(['error' => true]);
    }
}
