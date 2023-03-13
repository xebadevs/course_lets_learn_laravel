<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Follow;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;

class UserController extends Controller
{
    public function storeAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|max:2000'
        ]);

        $user = auth()->user();

        $filename = $user->id . '-' . uniqid() . '.jpg';

        $imgData = Image::make($request->file('avatar'))->fit(120)->encode('jpg');
        Storage::put('public/avatars/' . $filename, $imgData);

        $oldAvatar = $user->avatar;

        $user->avatar = $filename;
        $user->save();

        if ($oldAvatar != "/fallback-avatar.jpg") {
            Storage::delete(str_replace("/storage/", "public/", $oldAvatar));
        }

        return back()->with('success', 'Congrats on the new avatar.');
    }

    public function showAvatarForm()
    {
        return view('avatar-form');
    }

    private function getSharedData($profile)
    {
        $currentlyFollowing = 0;

        if (auth()->check()) {
            $currentlyFollowing = Follow::where([['user_id', '=', auth()->user()->id], ['followeduser', '=', $profile->id]])->count();
        }

        View::share('sharedData', [
            'currentlyFollowing' => $currentlyFollowing,
            'avatar' => $profile->avatar,
            'username' => $profile->username,
            'postCount' => $profile->posts()->count()
        ]);
    }

    public function profile(User $profile)
    {
        $this->getSharedData($profile);

        return view('profile-posts', [
            'posts' => $profile->posts()->latest()->get(),
        ]);
    }

    public function profileFollowers(User $profile)
    {
        $this->getSharedData($profile);

        return view('profile-followers', [
            'followers' => $profile->followers()->latest()->get(),
        ]);
    }

    public function profileFollowing(User $profile)
    {
        $this->getSharedData($profile);

        return view('profile-following', [
            'following' => $profile->followingTheseUsers()->latest()->get(),
        ]);
    }

    public function logout()
    {
        auth()->logout();
        return redirect('/')->with('error', 'You have successfully logged out');
    }

    public function showCorrectHomepage()
    {
        if (auth()->check()) {
            return view('home-page-feed');
        } else {
            return view('home-page');
        }
    }

    public function login(Request $request)
    {
        $incomingFields = $request->validate([
            'loginusername' => 'required',
            'loginpassword' => 'required'
        ]);

        if (auth()->attempt([
            'username' => $incomingFields['loginusername'],
            'password' => $incomingFields['loginpassword']
        ])) {
            $request->session()->regenerate();
            return redirect('/')->with('success', 'You have successfully logged in');
        } else {
            return redirect('/')->with('failure', 'Invalid login');
        }
    }

    public function register(Request $request)
    {
        $incomingFields = $request->validate([
            'username' => ['required', 'min:3', 'max:20', Rule::unique('users', 'username')],
            'email' => ['required', 'email', Rule::unique('users', 'email')],
            'password' => ['required', 'min:6', 'confirmed']
        ]);

        $incomingFields['password'] = bcrypt($incomingFields['password']);

        $user = User::create($incomingFields);
        auth()->login($user);
        return redirect('/')->with('success', 'Thank you for creating an account');
    }
}
