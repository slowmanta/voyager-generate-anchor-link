<?php

use Illuminate\Database\Seeder;
use TCG\Voyager\Models\DataType;

class AnchorLinkDataTypesTableSeeder extends Seeder
{
    /**
     * Auto generated seed file.
     */
    public function run()
    {
        $dataType = $this->dataType('slug', 'anchor_link');
        if (!$dataType->exists) {
            $dataType->fill([
                'name'                  => 'anchor_link',
                'display_name_singular' => 'Generate Anchor Link',
                'display_name_plural'   => 'AnchorLink',
                'icon'                  => 'voyager-receipt',
                'model_name'            => 'NamMT\\VoyagerGenerateAnchorLink\\Http\\Models\\AnchorLink',
                'policy_name'           => '',
                'controller'            => 'NamMT\\VoyagerGenerateAnchorLink\\Http\\Controllers\\VoyagerGenerateAnchorLinkController',
                'generate_permissions'  => 1,
                'description'           => '',
            ])->save();
        }
    }

    /**
     * [dataType description].
     *
     * @param [type] $field [description]
     * @param [type] $for   [description]
     *
     * @return [type] [description]
     */
    protected function dataType($field, $for)
    {
        return DataType::firstOrNew([$field => $for]);
    }
}
