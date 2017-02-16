<?php

use App\Models\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $user = factory(\App\Models\User::class)->times(50)->make();
        \App\Models\User::insert($user->toArray());

        $user = User::find(1);
        $user->name = "伸长脖子的狼";
        $user->email = "794973775@qq.com";
        $user->password = '123456';
        $user->is_admin = true;
        $user->save();
        //$users = factory(User::class)->times(50)->make();
        //User::insert($users->toArray());
    }
}
