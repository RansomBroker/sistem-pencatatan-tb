<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function loginView()
    {
        return view('login');
    }

    public function userView()
    {
        return  view('users.user', ['users' => User::all()]);
    }

    public function userAddView()
    {
        return  view('users.user_add');
    }

    public function userEditView($id)
    {
        return view('users.user_edit', ['user' => User::find($id)]);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        if (Auth::attempt([
            'name' => $credentials['username'],
            'password' => $credentials['password']
        ])) {
            $request->session()->regenerate();
            return redirect()->intended('/');
        }

        return back()->withErrors([
            'invalid-data' => 'Username atau Password salah',
        ])->onlyInput('invalid-data');
    }

    public function userAdd(Request $request)
    {
        $validator = $request->validate([
            'username' => 'required|max:20',
            'role' => 'required',
            'password' => 'required'
        ]);

        $user = new User();
        $user->name = $validator['username'];
        $user->role = $validator['role'];
        $user->email = '';
        $user->password = Hash::make($validator['password']);

        if ($user->save()) {
            $request->session()->flash('status', 'success');
            $request->session()->flash('message', 'Berhasil menambahkan data');
        }else {
            $request->session()->flash('status', 'danger');
            $request->session()->flash('message', 'Gagal menambahkan data');
        }

        return redirect('user');
    }

    public function userEdit(Request $request)
    {
        $validator = $request->validate([
            'username' => 'required|max:20',
            'role' => 'required',
        ]);

        $user = User::find($request->id);
        $user->name = $validator['username'];
        $user->role = $validator['role'];
        $user->email = '';

        if ($request->password != null) {
            $user->password = Hash::make($validator['password']);
        }

        if ($user->save()) {
            $request->session()->flash('status', 'success');
            $request->session()->flash('message', 'Berhasil mengedit data');
        }else {
            $request->session()->flash('status', 'danger');
            $request->session()->flash('message', 'Gagal mengedit data');
        }

        return redirect('user');

    }

    public function userDelete($id)
    {
        $user = User::find($id);
        $userName = $user->name;
        try {
            $user->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Berhasil menghapus user '.$userName
            ]);
        } catch (QueryException $error) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Gagal menghapus user '.  $userName  .' dikarenakan cabang masih memiliki data'
            ]);
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        return redirect('/');
    }

}
