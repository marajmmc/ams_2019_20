<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Barcode_helper
{
    // asset
    public static function get_barcode_asset($prefix, $increment_id)
    {
        return $prefix.str_pad($increment_id,5,0,STR_PAD_LEFT);
    }

}
