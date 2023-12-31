<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Follower;
use App\Models\Post;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FrontendController extends Controller
{
    public function index()
    {
        $trendingPost = Post::all();
        $followerIds = Follower::where('following_id', auth()->user()->id)->pluck('follower_id');
        $allUser = User::where('id', '!=', auth()->user()->id)->whereNotIn('id', $followerIds)->inRandomOrder()->get();

        $allPost = Post::whereIn('user_id', $followerIds)->orWhere('user_id', auth()->user()->id)->latest()->get();

        $allFollower = Follower::where('follower_id', auth()->user()->id)->get();
        $allFollowing = Follower::where('following_id', auth()->user()->id)->get();

        return view('frontend.index', compact('allUser', 'allPost', 'trendingPost', 'allFollower', 'allFollowing'));
    }

    public function indexPostData()
    {
        $followerIds = Follower::where('following_id', auth()->user()->id)->pluck('follower_id');
        $allPost = Post::whereIn('user_id', $followerIds)->orWhere('user_id', auth()->user()->id)->latest()->get();

        return view('frontend.post.index', compact('allPost'));
    }

    public function follower()
    {
        $allFollower = Follower::where('follower_id', auth()->user()->id)->get();
        $allFollowing = Follower::where('following_id', auth()->user()->id)->get();
        return view('frontend.follower', compact('allFollower', 'allFollowing'));
    }
    public function following()
    {
        $allFollower = Follower::where('follower_id', auth()->user()->id)->get();
        $allFollowing = Follower::where('following_id', auth()->user()->id)->get();
        return view('frontend.following', compact('allFollowing', 'allFollower'));
    }

    public function followerStatus($id)
    {
        $followerStatus = Follower::where('follower_id', $id)->where('following_id', Auth::user()->id)->first();
        if(!$followerStatus){
            Follower::insert([
                'follower_id' => $id,
                'following_id' => Auth::user()->id,
                'created_at' =>Carbon::now(),
            ]);
        }else{
            $followerStatus->delete();
        }
    }

    public function timeline ($username)
    {
        $user = User::where('username', $username)->first();
        $follower_count = Follower::where('follower_id', $user->id)->count();
        $following_count = Follower::where('following_id', $user->id)->count();

        $allPost = Post::where('user_id', $user->id)->get();

        $allFollower = Follower::where('follower_id', auth()->user()->id)->get();
        $allFollowing = Follower::where('following_id', auth()->user()->id)->get();

        $followStatus = Follower::where('follower_id', $user->id)->where('following_id', auth()->user()->id)->first();

        return view('frontend.profile.timeline', compact('user', 'follower_count', 'following_count', 'allPost', 'allFollower', 'allFollowing', 'followStatus'));
    }

    public function timelinePostData($username)
    {
        $user = User::where('username', $username)->first();

        $allPost = Post::where('user_id', $user->id)->get();

        return view('frontend.post,index', compact('allPost'));
    }
}
