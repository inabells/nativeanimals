<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    /**
     * Create new UserController instance
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Redirect user to a specified controller according to role
     *
     * @return  Redirect/View
     */
    public function index(Request $request)
    {
        $user = $request->user();
        if($user->hasRole('admin')) return redirect()->action('AdminController@index');
        else if($user->hasRole('farm')) return redirect()->action('FarmController@index');
        else redirect()->route('logout');

    }
}
