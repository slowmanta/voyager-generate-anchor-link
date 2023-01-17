<?php

use Illuminate\Database\Seeder;

class VoyagerGenerateAnchorLinkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            AnchorLinkDataTypesTableSeeder::class,
            AnchorLinkDataRowsTableSeeder::class,
            AnchorLinkPermissionSeeder::class,
        ]);
    }
}
