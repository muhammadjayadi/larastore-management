<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $users = \App\User::paginate(10);

        // filter keyword
        $filterKeyword = $request->get("keyword");
        $status = $request->get("status");
        if ($filterKeyword) {
            if ($status) {
                $users = \App\User::where('email', 'LIKE', "%$filterKeyword%")->where('status', $status)->paginate(10);
            } else {
                $users = \App\User::where('email', 'LIKE', "%$filterKeyword%")->paginate(10);
            }
        }

        return view("users.index", compact("users"));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view("users.create");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $newUser = new \App\User;
        $newUser->name = $request->get("name");
        $newUser->username = $request->get("username");
        $newUser->roles = json_encode($request->get("roles"));
        $newUser->phone = $request->get("phone");
        $newUser->address = $request->get("address");
        if ($request->file("avatar")) {
            $file = $request->file("avatar")->store("avatars", "public");
            $newUser->avatar = $file;
        }
        $newUser->email = $request->get("email");
        $newUser->password = \Hash::make($request->get("password"));

        $newUser->save();

        return redirect()->route("users.index")->with("status", "User succesfully added");
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = \App\User::findOrFail($id);

        return view("users.show", compact("user"));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = \App\User::findOrFail($id);

        return view("users.edit", compact("user"));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = \App\User::findOrFail($id);
        $user->name = $request->get("name");
        $user->username = $request->get("username");
        $user->status = $request->get("status");
        $user->roles = json_encode($request->get("roles"));
        $user->phone = $request->get("phone");
        $user->address = $request->get("address");
        if ($request->file("avatar")) {
            if ($user->avatar && file_exists(storage_path("app/public/" . $user->avatar))) {
                \Storage::delete("public/" . $user->avatar);
            }
            $file = $request->file("avatar")->store("avatars", "public");
            $user->avatar = $file;
        }
        $user->email = $request->get("email");

        $user->save();

        return redirect()->route("users.index")->with("status", "User succesfully updated");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = \App\User::findOrFail($id);
        $user->delete();
        return redirect()->route("users.index")->with("status", "User succesfully deleted");
    }
}
