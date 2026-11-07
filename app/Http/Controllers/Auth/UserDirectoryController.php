<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\ListUsersAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserDirectoryController extends Controller
{
    public function index(Request $request, ListUsersAction $list): View
    {
        $status = in_array($request->query('status'), ['approved', 'pending', 'rejected'], true)
            ? $request->query('status')
            : 'all';
        $search = $request->query('q');

        return view('admin.users.index', [
            'status' => $status,
            'search' => $search,
            'users' => $list->execute($status, $search),
            'counts' => $list->counts(),
        ]);
    }
}