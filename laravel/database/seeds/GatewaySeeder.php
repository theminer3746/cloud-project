<?php

use Illuminate\Database\Seeder;

class GatewaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('gateways')->insert([
            'user_id' => 1,
            'activation_key' => bin2hex(random_bytes(10)),
            'arn' =>  bin2hex(random_bytes(10)),
            'name' => 'test',
            'real_name' => 'real_gateway_name',
            'type' => 'FILE_S3',
        ]);
    }
}
