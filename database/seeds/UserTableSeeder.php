<?php

use App\Models\User;
use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $users = factory(User::class)->times(50)->make();
        User::insert($users->toArray());

        $user = User::findOrFail(1);
        $user->name = "伸长脖子的狼";
        $user->email = "794973775@qq.com";
        $user->is_admin = true;
        $user->password = '123456';
        $user->activated = true;
        $user->save();
    }
}
