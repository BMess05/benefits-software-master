<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserAddRequest;
use App\Http\Requests\UserEditRequest;

class UserController extends Controller
{
    public function listStandardUsers()
    {
        $users = resolve('users')->getAllStandardUsers();
        return view('admin.users.list', compact('users'));
    }

    public function addUser()
    {
        return view('admin.users.add');
    }

    public function saveUser(UserAddRequest $request)
    {
        $data = $request->all();
        $result = resolve('users')->saveUser($data);
        if ($result) {
            return redirect()->route('listStandardUsers')->with(['status' => 'success', 'message' => 'User added successfully']);
        } else {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Something went wrong. Please try again.']);
        }
    }

    public function editUser($id = null)
    {
        if ($id == null) {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Invalid Input']);
        }
        $user = resolve('users')->getUserById($id);
        if (!$user) {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Invalid Input']);
        }
        return view('admin.users.edit', compact('user'));
    }

    public function updateUser($id = null, UserEditRequest $request)
    {
        if ($id == null) {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Invalid Input']);
        }
        $user = resolve('users')->getUserById($id);
        if (!$user) {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Invalid Input']);
        }
        $data = $request->all();

        $result = resolve('users')->updateUser($id, $data);
        if ($result['status']) {
            return redirect()->route('listStandardUsers')->with(['status' => 'success', 'message' => 'User updated successfully']);
        } else {
            return redirect()->back()->with(['status' => 'danger', 'message' => $result['message']]);
        }
    }

    public function deleteUser($id = null)
    {
        if ($id == null) {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Invalid Input']);
        }
        $user = resolve('users')->getUserById($id);
        if (!$user) {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Invalid Input']);
        }
        $result = resolve('users')->deleteUser($id);
        if ($result) {
            return redirect()->route('listStandardUsers')->with(['status' => 'success', 'message' => 'User deleted successfully']);
        } else {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Something went wrong. Please try again.']);
        }
    }
}
