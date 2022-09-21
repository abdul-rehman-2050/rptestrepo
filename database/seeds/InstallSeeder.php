<?php

use Illuminate\Database\Seeder;

class InstallSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('stores')->insert([
            'name' => 'Main Store',
            'address'=>'',
            'created_by'=>1,
        ]);
        
        DB::table('tax_rates')->insert([
            'name' => 'No Tax',
            'code'=>'NT',
            'rate'=>0,
            'type'=>2,
            'created_by'=>1,
        ]);
        
        
        
        
        

    }
}
