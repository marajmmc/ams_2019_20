<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Stock_helper
{
    public static function get_variety_stock($variety_ids=array())
    {
        $CI =& get_instance();
        $CI->db->from($CI->config->item('table_sms_stock_summary_variety'));
        if(sizeof($variety_ids)>0)
        {
            $CI->db->where_in('variety_id',$variety_ids);
        }
        $results=$CI->db->get()->result_array();
        $stocks=array();
        foreach($results as $result)
        {
            $stocks[$result['variety_id']][$result['pack_size_id']][$result['warehouse_id']]=$result;
        }
        return $stocks;
    }

    public static function get_raw_stock($variety_ids=array())
    {
        $CI =& get_instance();
        $CI->db->from($CI->config->item('table_sms_stock_summary_raw'));
        if(sizeof($variety_ids)>0)
        {
            $CI->db->where_in('variety_id',$variety_ids);
        }
        $results=$CI->db->get()->result_array();
        $stocks=array();
        foreach($results as $result)
        {
            $stocks[$result['variety_id']][$result['pack_size_id']][$result['packing_item']]=$result;
        }
        return $stocks;
    }
    public static function get_variety_stock_outlet($outlet_id, $variety_ids=array())
    {
        $CI =& get_instance();
        $CI->db->from($CI->config->item('table_pos_stock_summary_variety').' pos_stock_summary_variety');
        $CI->db->where('pos_stock_summary_variety.outlet_id',$outlet_id);
        if(sizeof($variety_ids)>0)
        {
            $CI->db->where_in('variety_id',$variety_ids);
        }
        $results=$CI->db->get()->result_array();
        $stocks=array();
        foreach($results as $result)
        {
            $stocks[$result['variety_id']][$result['pack_size_id']]=$result;
        }
        return $stocks;
    }
}
