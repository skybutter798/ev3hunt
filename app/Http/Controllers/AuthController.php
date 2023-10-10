<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Exception;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;


class AuthController extends Controller
{
    public function redirectToTwitter()
    {
         Log::channel('user_info')->info('Redirecting to Twitter for authentication...');
        return Socialite::driver('twitter')->redirect();
    }
    
    

    public function handleTwitterCallback()
    {
        try {
            $user = Socialite::driver('twitter')->user();
            Log::channel('user_info')->info('Received user data from Twitter:', ['user' => $user]);

            // Extract the relevant data
            $userData = [
                'twitter_id' => $user->getId(),
                'name' => $user->getName(),
                'nickname' => $user->getNickname(),
                'avatar' => $user->getAvatar(),
                'email' => $user->getEmail(), // Note: Email might be null for Twitter
                'profile_banner_url' => isset($user->user['profile_banner_url']) ? $user->user['profile_banner_url'] : null,
                'location' => $user->user['location'],
                'description' => $user->user['description'],
                'followers_count' => $user->user['followers_count'],
                'friends_count' => $user->user['friends_count'],
                'statuses_count' => $user->user['statuses_count'],
                'created_at_twitter' => Carbon::parse($user->user['created_at']),
            ];
            
            // Update or create a user record in the database
            $localUser = User::updateOrCreate(
                ['twitter_id' => $userData['twitter_id']],
                $userData
            );
            
            // Log the user in
            Auth::login($localUser, true);
            
            return redirect('/');
            
        } catch (Exception $e) {
            Log::error('Error during Twitter authentication:', ['error' => $e->getMessage()]);
            return $e->getMessage();
        }
    }
    
    public function logout() {
        Auth::logout();
        return redirect('/');
    }



}
