<?php

namespace App\Http\Controllers\Auth;

use Auth;
use App\User;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = RouteServiceProvider::HOME;
    
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function callback()
    {
        return view('callback');
    }

    public function authenticate(Request $request)
    {
        $token = $request->only('token_type', 'access_token');
        if (count($token) <= 0) {
            return redirect(session('home_url'));
        }
        $client = new Client(['verify' => false]);
        $headers = ['Authorization' => ucfirst(implode(' ', $token)), 'Accept' => 'application/json'];
        $response = $client->request('GET', 'https://api.twitch.tv/helix/users', ['headers' => $headers]);
        if (
            $response->getStatusCode() < 200
            ||
            $response->getStatusCode() >= 300
        ) {
            return redirect(session('home_url'));
        }
        $twitch = json_decode($response->getBody()->getContents())->data[0];
        $user = User::updateOrCreate(['user_id' => $twitch->id], [
            'username' => $twitch->display_name,
            'token' => ucfirst(implode(' ', $token)),
            'email' => $twitch->email,
            'profile_image' => $twitch->profile_image_url
        ]);
        Auth::loginUsingId($user->id);
        return redirect(session('home_url'));
    }

    public function logout()
    {
        Auth::logout();
        return redirect(session('home_url'));
    }
}
