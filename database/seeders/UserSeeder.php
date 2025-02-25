<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $role=Role::create([
            'name'=>'super_admin',
            'guard_name'=>'web'
        ]);
        $user=User::create([
            'name'=>'Web Admin',
            'email'=>'admin@admin.com',
            'email_verified_at'=>date('Y-m-d h:i:s'),
            'password'=>Hash::make('password'),
            //'role_id'=>1,
            'can_publish'=>1
        ]);
        DB::table('model_has_roles')->insert([
            'role_id'=>$role->id,
            'model_type'=>User::class,
            'model_id'=>$user->id
        ]);
    }
}
