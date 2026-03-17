<?php

namespace App\Http\Controllers;

use App\Constants\Roles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TrashController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();

        // Only instructors and admins can access trash
        if (!in_array($user->role, [Roles::INSTRUCTOR, Roles::ADMIN])) {
            abort(403, 'Unauthorized access.');
        }

        return view('trash.index');
    }
}
