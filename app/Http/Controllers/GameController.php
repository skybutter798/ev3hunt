<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Grid;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Abraham\TwitterOAuth\TwitterOAuth;
use Illuminate\Support\Facades\Log;

class GameController extends Controller
{
    public function index()
    {
        $remainingClicks = 0; // Default value
    
        if (Auth::check()) {
            $user = Auth::user();
    
            // Check how many grids the user has clicked today
            $todayClicks = DB::table('grids')
                         ->where('user_id', $user->id)
                         ->whereDate('updated_at', Carbon::now('Asia/Kuala_Lumpur')->toDateString())
                         ->count();
    
            $remainingClicks = 100 - $todayClicks;
        }
    
        $grids = Grid::all();
        return view('grid', compact('grids', 'remainingClicks'));
    }


    public function checkGrid(Request $request)
    {
        $user = Auth::user();
    
        // Check how many grids the user has clicked today
        $todayClicks = DB::table('grids')
                     ->where('user_id', $user->id)
                     ->whereDate('updated_at', Carbon::now('Asia/Kuala_Lumpur')->toDateString())
                     ->count();
    
        if ($todayClicks >= 100) {
            return response()->json(['message' => 'You have reached your click limit for today.']);
        }
    
        $gridId = $request->input('id');
        $grid = Grid::find($gridId);
    
        if (!$grid) {
            return response()->json(['message' => 'Invalid grid.']);
        }
    
        if ($grid->clicked) {
            return response()->json(['message' => 'Grid already clicked.']);
        }
    
        // Update the grid's clicked status and user_id
        $grid->clicked = true;
        $grid->user_id = Auth::id();
        $grid->save();
    
        if ($grid->reward_item_id) {
            // You can customize this to return the actual reward title or any other details.
            return response()->json(['message' => 'Congratulations! You found a reward!']);
        }
    
        return response()->json(['message' => 'Try next time']);
    }

    
    public function checkForTweet($userId) {
        $user = User::find($userId);
    
        $connection = new TwitterOAuth(
            env('TWITTER_API_KEY'), 
            env('TWITTER_API_SECRET'),
            $user->token,  // Assuming you saved the user's Twitter token
            $user->tokenSecret  // Assuming you saved the user's Twitter token secret
        );
    
        $tweets = $connection->get("statuses/user_timeline", ["count" => 10, "exclude_replies" => true]);
    
        foreach ($tweets as $tweet) {
            if (strpos($tweet->text, '#EV3') !== false) {
                // The tweet contains the hashtag
                // Grant the user an extra click and break out of the loop
                $this->grantExtraClick($userId);
                break;
            }
        }
    }
    
    public function grantExtraClick($userId) {
         Log::info('one more click');
    }

}
