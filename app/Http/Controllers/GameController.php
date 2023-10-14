<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Grid;
use App\Models\Reward;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Abraham\TwitterOAuth\TwitterOAuth;
use Illuminate\Support\Facades\Log;


class GameController extends Controller
{
    public function index()
    {
        $remainingClicks = 0; // Default value
        $remain = \App\Models\Grid::where('clicked', 0)->where('reward_item_id', 1)->get();
        $password = config('assets.password');
    
        if (Auth::check()) {
            $user = Auth::user();
    
            // Check how many grids the user has clicked today
            $todayClicks = DB::table('grids')
                         ->where('user_id', $user->id)
                         ->whereDate('updated_at', Carbon::now('Asia/Kuala_Lumpur')->toDateString())
                         ->count();
                         
            if ($todayClicks >2){
                $remainingClicks = 0;
            } else {
                $remainingClicks = 2 - $todayClicks;
            }
        }
    
        $grids = Grid::all();
        return view('grid', compact('grids', 'remainingClicks', 'remain', 'password'));
    }
    
    public function beta()
    {
        $remainingClicks = 0; // Default value
        $remain = \App\Models\Grid::where('clicked', 0)->where('reward_item_id', 1)->get();
    
        if (Auth::check()) {
            $user = Auth::user();
    
            // Check how many grids the user has clicked today
            $todayClicks = DB::table('grids')
                         ->where('user_id', $user->id)
                         ->whereDate('updated_at', Carbon::now('Asia/Kuala_Lumpur')->toDateString())
                         ->count();
                         
            if ($todayClicks >2){
                $remainingClicks = 0;
            } else {
                $remainingClicks = 2 - $todayClicks;
            }
        }
    
        $grids = Grid::all();
        return view('beta', compact('grids', 'remainingClicks', 'remain'));
    }


    public function checkGrid(Request $request)
    {
        $user = Auth::user();
    
        $todayClicks = DB::table('grids')
                     ->where('user_id', $user->id)
                     ->whereDate('updated_at', Carbon::now('Asia/Kuala_Lumpur')->toDateString())
                     ->count();
    
        if ($todayClicks >= 2 && $user->share == 0) {
            return response()->json(['message' => 'limit']);
        } elseif ($todayClicks >= 3 || ($todayClicks >= 2 && $user->share == 2)) {
            return response()->json(['message' => 'shared']);
        }

    
        $gridId = $request->input('id');
        $grid = Grid::find($gridId);
    
        if (!$grid) {
            return response()->json(['message' => 'Invalid grid.']);
        }
    
        if ($grid->clicked) {
            return response()->json(['message' => 'repeat']);
        }
    
        $hasWalletAddress = $request->input('hasWalletAddress');

        if (!$hasWalletAddress) {
            $grid->clicked = true;
        }
        $grid->user_id = Auth::id();
        $grid->save();
        if ($user->share == 1) {
            $user->share = 2;
            $user->save();
        }
        
        // Log the click
        $userId = Auth::id();
        $userName = $user->name;
        Log::channel('grid_clicks')->info("User {$userId}, {$userName} clicked on grid {$gridId}");
    
        if ($grid->reward_item_id) {
            switch ($grid->reward_item_id) {
                case 1:
                    return response()->json(['message' => 'reward']);
                    break;
                case 2:
                    return response()->json(['message' => 'cash']);
                    break;
                case 3:
                    return response()->json(['message' => 'airdrop']);
                    break;
                default:
                    return response()->json(['message' => 'goldenticket']);
                    break;
            }
        }


        return response()->json(['message' => 'none']);
    }
    
    public function updateStatus(Request $request) {
        $user = Auth::user();
        if ($user->share == 0) {
            $user->share = 1;
        } elseif ($user->share == 1) {
            $user->share = 2;
        }
        $user->save();
        return response()->json(['message' => 'Share status updated']);
    }


    
    public function saveWalletAddress(Request $request)
    {
        $request->validate([
            'wallet_address' => 'required|string|regex:/^0x[a-fA-F0-9]{40}$/'
        ]);
    
        $user = Auth::user();
        $user->wallet_address = $request->wallet_address;
        $user->save();
    
        return response()->json(['success' => true, 'message' => 'Wallet address saved successfully!']);
    }
    
    public function recordReward(Request $request) {
        try {
            $userId = $request->input('user_id');
            $rewardType = $request->input('reward_type');

            if (Auth::id() == $userId) {
                DB::table('rewards')->insert([
                    'user_id' => $userId,
                    'reward_type' => $rewardType,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                return response()->json(['success' => true, 'message' => 'Reward recorded successfully']);
            } else {
                return response()->json(['success' => false, 'message' => 'Unauthorized action'], 403);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error recording the reward: ' . $e->getMessage()], 500);
        }
    }
    
   public function checkWinStatus(Request $request) {
        try {
            $userId = $request->input('user_id');
    
            if (Auth::id() == $userId) {
                $hasWon = DB::table('rewards')->where('user_id', $userId)->exists();
    
                if ($hasWon) {
                    return response()->json(['success' => true, 'message' => 'Good luck man, but you already registered in the database, so no reward this time.']);
                } else {
                    return response()->json(['success' => false, 'message' => 'User has not won yet.']);
                }
            } else {
                return response()->json(['success' => false, 'message' => 'Unauthorized action'], 403);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error checking win status: ' . $e->getMessage()], 500);
        }
    }
    
    public function watercave()
    {
        $user = Auth::user(); // Get the currently authenticated user
    
        // Check the watercave status
        $hasJoined = $user->watercave == 1;
    
        return view('watercave', ['hasJoined' => $hasJoined]);
    }

    
    public function updateWatercave(Request $request) {
        $user = Auth::user();
    
        $user->watercave = 1; 
        $user->save();
    
        return response()->json(['success' => true, 'message' => 'Watercave status updated successfully']);
    }
}
