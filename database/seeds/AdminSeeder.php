<?php

use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\User::create([
            'id'=> 1,
        	'nik' => 3562730001,
        	'nama' => 'Yosieka Putri Wibawa',
        	'telp' => '082334576891',
            'username' => 'cika',
            'password' => bcrypt('123456'),
        	'level' => 'admin'
        ]);
    }
}
