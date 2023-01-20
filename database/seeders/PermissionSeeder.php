<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $pr=[];
        $roles=[
            'admin',
            'employee',
            'visitor'
        ];
        $permissions=[
            //admin
            'add-role',
            'delete-role',
            'view-users',
            'edit-user',
            //employee
            'add-movie',
            'edit-movie',
            'delete-movie',
            'add-video',
            'edit-video',
            'delete-video',
            'add-category',
            'edit-category',
            'delete-category',
            'view-rating',
            //visitor
            'view-movie',
            'view-video',
            'view-category',
            'add-rating',
            'edit-rating',
            'delete-rating'
        ];
        $ro=[];
        foreach ($roles as $r){
            $ro[]=Role::create(['name' => $r]);
        }
        foreach ($permissions as $p){
            $pr[]=Permission::create(['name' => $p]);
        }
        foreach ($ro as $i=>$role){
            foreach ($pr as $j=>$permission){
                if($i == 0){
                    $role->givePermissionTo($permission);
                }elseif ($i == 1 && $j>3){
                    $role->givePermissionTo($permission);
                }elseif ($i == 2 && $j>13){
                    $role->givePermissionTo($permission);
                }
            }
        }

//        foreach ($ro as $r=>$role){
//            foreach ($pr as $p=>$permission){
//                if ($r==0){
//                    $role->givePermissionTo($permission);
//                }elseif ($r==1 && $p>3){
//                    $role->givePermissionTo($permission);
//                }elseif($r==2 && $p>14){
//                    $role->givePermissionTo($permission);
//                }
//            }
//        }
        $dataAdmin=[
            "name"=>"komy",
            "email"=>"komy@gmail.com",
            "password"=>Hash::make("123123123"),
            "phone"=>"123123123",
            "address"=>"123123312",
            "isMale"=>"1",
            "uniqueId"=>Str::random(32),
        ];
        $admin=new User($dataAdmin);
        $admin->save();
        $role= Role::findByName('admin');
        $admin->assignRole($role);

        $dataEmployee=[
            "name"=>"hafed",
            "email"=>"hafed@gmail.com",
            "password"=>Hash::make("123123123"),
            "phone"=>"123123123",
            "address"=>"123123312",
            "isMale"=>"1",
            "uniqueId"=>Str::random(32),
        ];
        $employee=new User($dataEmployee);
        $employee->save();
        $role= Role::findByName('employee');
        $employee->assignRole($role);


        $dataVisitor=[
            "name"=>"bshar",
            "email"=>"bshar@gmail.com",
            "password"=>Hash::make("123123123"),
            "phone"=>"123123123",
            "address"=>"123123312",
            "isMale"=>"1",
            "uniqueId"=>Str::random(32),
        ];
        $visitor=new User($dataVisitor);
        $visitor->save();
        $role= Role::findByName('visitor');
        $visitor->assignRole($role);

    }
}
