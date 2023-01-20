<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\UserService;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\FlareClient\Http\Exceptions\NotFound;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;


class UserController extends Controller
{
    public function Register(Request $request)
    {
        try {
            $data = $request->only(["name", "email", "password", 'password_confirmation', "phone", "address", "photo", "isMale",]);
            $request->validate([
                'name' => ['string', 'required'],
                'email' => ['email', 'required', 'unique:users'],
                'password' => ['required', 'string', 'confirmed', 'min:8'],
                'phone' => ['required','string'],
                'address' => ['required', 'string'],
                'isMale' => ['boolean'],
                'photo' => ['file', 'mimes:jpg,png'],
            ], $data);
            $data['uniqueId'] = Str::random(32);
            $data['password'] = Hash::make($data['password']);
            $data['published'] = false;
            if ($request->has('photo')) {
//                $data['photo']=$request->file('photo')->store('/photos');
//                $data['photo']=Storage::put('/photos',$request->file('photo'));
                $data['photo'] = (new UserService())->saveFile('photo', '/photos', $request->allFiles());
            }
//            $u = new User($data);
//            return $u->save();


            if (!(new UserService())->save($data)) {
                throw new \Exception('failed to save');
            }
            $role= Role::findByName('visitor');
            $user= (new UserService())->getFirst(["uniqueId"=>$data['uniqueId']]);
            $user->assignRole($role);
            Auth::attempt([
                'email' => $data['email'],
                'password' => $data['password_confirmation'],

            ]);
            return response()->json([
                'success' => 1,
                'msg' => 'successfully',
                'token' => \auth()->user()->createToken($request->ip())->plainTextToken,
                'uniqueId'=>$data['uniqueId'],
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'success' => 0,
                'msg' => $exception->getMessage(),
            ]);
        }
    }

    public function Login(Request $request)
    {
        try {
            $data = $request->only(['email', 'password']);
            $request->validate([
                'email' => ['email', 'required'],
                'password' => ['string', 'required', 'min:8']
            ], $data);
            if (!Auth::attempt([
                'email' => $data['email'],
                'password' => $data['password']
            ])) {
                throw new \Exception('filed to login');
            }
            return response()->json([
                'success' => 1,
                'msg' => 'successfully',
                'token' => \auth()->user()->createToken($request->ip())->plainTextToken,
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'success' => 0,
                'msg' => $exception->getMessage(),
            ]);
        }
    }

    public function logout()
    {
        try {
            \auth()->user()->tokens()->delete();
            return response()->json([
                'success' => 1,
                'msg' => 'successfully1',
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'success' => 0,
                'msg' => $exception->getMessage(),
            ]);
        }
    }

    public function ChangPassword(Request $request)
    {
        try {
            $data = $request->only(['password', 'password_confirmation', 'old_password']);
            $request->validate([
                'password' => ['required', 'string', 'confirmed', 'min:8'],
                'old_password' => ['required', 'string', 'min:8']
            ], $data);
            $data['password'] = Hash::make($data['password']);
            $user = \auth()->user();
            if (password_verify($data['old_password'], $user['password'])) {
                if (!(new UserService())->update(['password' => $data['password']], ['uniqueId' => $user->uniqueId])) {
                    throw new \Exception('wrong ');
                }
            } else {
                throw new \Exception('wrong password');
            }
            return response()->json([
                'success' => 1,
                'msg' => 'successfully',
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'success' => 0,
                'msg' => $exception->getMessage(),]);
        }
    }

    public function profile($uniqueId = null)
    {
//        try {
//
//                if ($uniqueId != null) {
//                    $user = (new UserService())->getFirst(['uniqueId' => $uniqueId]);
//                    if (!$user) throw new NotFound('user not found');
//                } else {
//                    if (!\auth('sanctum')->check()) throw new AuthenticationException();
//                    $user = \auth('sanctum')->user();
//                }
//                $user = array_merge($user->toArray());
//                return$user;
//            }catch (\Exception $exception) {
//            return response()->json([
//                'success' => 0,
//                'msg' => $exception->getMessage(),]);
//        }
        return response()->json([
            'success' => 1,
            'user' => \auth()->user(),
        ]);


    }


    public function view(Request $request, $uniqueId = null)
    {
        try {
            $data = $request->only(['name', 'email', 'search']);
            $request->validate([
                'name' => ['string'],
                'search' => ['string'],
                'email' => ['email'],
            ], $data);
            $users = (new UserService())->getListQuery();
            if ($uniqueId != null) {
                $users = $users->where('uniqueId', $uniqueId);
            }
            if ($request->has('name')) {
                $users = $users->where('name', $data['name']);
            }
            if ($request->has('search')) {
                $users = (new UserService())->getListQuery(['keyword' => $data['search']]);
            }
            if ($request->has('email')) {
                $users = $users->where('email', $data['email']);
            }
            return response()->json([
                'success' => 1,
                'users' => $users->get(),
            ]);

        } catch (\Exception $exception) {
            return response()->json([
                'success' => 0,
                'msg' => $exception->getMessage(),]);
        }


    }
    public function addPermission(Request $request){
        try {
            $data=$request->only(["uniqueId","permissionName"]);
            $request->validate([
                "uniqueId"=>['string','required'],
                "permissionName"=>['string','required'],
            ],$data);
            $permissionName=$data['permissionName'];
          if (!($user= (new UserService())->getFirst(["uniqueId"=>$data['uniqueId']]))) {
              throw new \Exception('the user not found');
          }
        $per=  Permission::query()->select('name')->where('name',$permissionName)->firstOrFail();
            $user->givePermissionTo($permissionName);
//          if (!($user->givePermissionTo($permissionName))){
//               throw new \Exception('the permission is not change ');
//           }
            return response()->json([
                'success' => 1,
                'users' => 'successfully',
            ]);

        }catch (\Exception $exception) {
            return response()->json([
                'success' => 0,
                'msg' => $exception->getMessage(),]);
        }

    }
    public function deletePermission(Request $request){
        try {
            $data=$request->only(["uniqueId","permissionName"]);
            $request->validate([
                "uniqueId"=>['string','required'],
                "permissionName"=>['string','required'],
            ],$data);
            $permissionName=$data['permissionName'];
            if (!($user= (new UserService())->getFirst(["uniqueId"=>$data['uniqueId']]))) {
                throw new \Exception('the user not found');
            }
            if (!($user->revokePermissionTo($permissionName))){
                throw new \Exception('the permission is not remove ');
            }
            return response()->json([
                'success' => 1,
                'users' => 'successfully',
            ]);

        }catch (\Exception $exception) {
            return response()->json([
                'success' => 0,
                'msg' => $exception->getMessage(),]);
        }
    }
    public function editRole(Request $request){
        try {
            $data=$request->only(["uniqueId","oldRoleName","newRole"]);
            $request->validate([
                "uniqueId"=>['string','required'],
                "oldRoleName"=>['string','required'],
                "newRole"=>['string','required'],
            ],$data);
            $oldRoleName=$data['oldRoleName'];
            $newRole=$data['newRole'];

            $role=Role::query()->where("name",$newRole)->firstOrFail(['name']);
            if (!($user= (new UserService())->getFirst(["uniqueId"=>$data['uniqueId']]))) {
                throw new \Exception('the user not found');
            }

            if (!($user->removeRole($oldRoleName))){
                throw new \Exception('the Role is not remove ');
            }

            if (!( $user->assignRole($role->name))){
                throw new \Exception('the new role is not assign');
            }
            return response()->json([
                'success' => 1,
                'users' => 'successfully',
            ]);

        }catch (\Exception $exception) {
            return response()->json([
                'success' => 0,
                'msg' => $exception->getMessage(),]);
        }
    }
    public function viewPermission(Request $request){
        try {
            $data=$request->only(['uniqueId']);
            $request->validate([
                "uniqueId"=>["string","required"],
            ],$data);
//            $users = (new UserService())->getListQuery();
//            $users = $users->where('uniqueId', $data['uniqueId']);
            $user= (new UserService())->getFirst(["uniqueId"=>$data['uniqueId']]);

            $namePermissions=$user->getAllPermissions();
            $p=[];
//            $p=array_column($namePermissions, 'name');

            foreach ($namePermissions as $i){
                $p[]= $i->name;
            }
            return response()->json([
                'success' => 1,
                'per'=>$p
            ]);
        }catch (\Exception $exception) {
            return response()->json([
                'success' => 0,
                'msg' => $exception->getMessage(),]);
        }

    }
}




