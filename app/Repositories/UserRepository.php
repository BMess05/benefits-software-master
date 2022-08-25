<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Hash;
use App\User;

class UserRepository
{
    public function getAllStandardUsers()
    {
        return User::where('role', 1)->orderBy('id', 'DESC')->get();
    }

    public function saveUser($data = [])
    {
        if (empty($data)) {
            return false;
        }
        $user = new User();
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->password = Hash::make($data['password']);
        $user->role = 1;
        if (isset($data['status'])) {
            $user->status = 1;
        } else {
            $user->status = 0;
        }
        if ($user->save()) {
            return true;
        }
        return false;
    }

    public function getUserById($id = null)
    {
        if ($id == null) {
            return false;
        }
        return User::find($id);
    }

    public function updateUser($id = null, $data = [])
    {
        $user = User::find($id);
        if (!$user) {
            return ['status' => false, 'message' => 'User not found'];
        }
        $user->name = $data['name'];
        $user->email = $data['email'];
        if (isset($data['pswd']) && trim($data['pswd']) != "") {
            if (strlen($data['pswd']) < 8) {
                return ['status' => false, 'message' => 'Password could not be less that 8 characters'];
            }
            $user->password = bcrypt($data['pswd']);
        }
        if (isset($data['status'])) {
            $user->status = 1;
        } else {
            $user->status = 0;
        }
        if ($user->save()) {
            return ['status' => true, 'message' => 'success'];
        }
        return ['status' => false, 'message' => 'Could not update a user.'];
    }

    public function deleteUser($id = null)
    {
        $user = User::find($id);
        if (!$user) {
            return false;
        }
        $res = User::where('id', $id)->delete();
        if ($res) {
            return true;
        } else {
            return false;
        }
    }
}
