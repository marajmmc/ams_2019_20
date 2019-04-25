<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Category_helper
{
    // For Item Category
    public static function get_category_parent_children()
    {
        $CI =& get_instance();
        $category = array();
        $category['parents'] = array();
        $category['children'] = array();
        $category['category'] = array();

        $parent_immediate = array();

        $results = Query_helper::get_info($CI->config->item('table_ams_setup_categories'), array('*'), array(), 0, 0, array('ordering ASC'));
        foreach ($results as $result)
        {
            $category['category'][$result['id']] = $result;
            $category['children'][$result['parent']][] = $result['id'];

            $parent_immediate[$result['id']] = $result['parent']; // Generate Immediate Parent Array
        }
        foreach ($results as $result)
        {
            Category_helper::get_category_parents($result['id'], $parent_immediate, $category);
        }
        return $category;
    }

    public static function get_category_parents($category_id, $parent_immediate, &$categories)
    {
        $current_id = $category_id;
        do
        {
            $categories['parents'][$category_id][] = $parent_immediate[$current_id];
            $current_id = $parent_immediate[$current_id];
        } while ($current_id != 0);
    }

    public static function get_category_tree_list($id = 0) //Sub Locations Dropdown list
    {
        $CI =& get_instance();
        if ($id > 0)
        {
            $CI->db->where('id !=', $id);
        }
        else
        {
            $CI->db->where('status', $CI->config->item('system_status_active'));
        }
        $CI->db->order_by('ordering');
        $results = $CI->db->get($CI->config->item('table_ams_setup_categories'))->result_array();

        $children = array();
        foreach ($results as $result)
        {
            if ($result['status'] == $CI->config->item('system_status_inactive'))
            {
                $result['name'] .= ' (' . $result['status'] . ')';
            }
            $children[$result['parent']]['ids'][$result['id']] = $result['id'];
            $children[$result['parent']]['children'][$result['id']] = $result;
        }

        $tree = array();
        if (isset($children[0]))
        {
            $level_0 = $children[0]['children'];
            foreach ($level_0 as $row)
            {
                Category_helper::get_sub_tree_list_for_dropdown($row, '', $tree, $children);
            }
        }
        return $tree;
    }

    // For Item Location
    public static function get_location_parent_children()
    {
        $CI =& get_instance();
        $location = array();
        $location['parents'] = array();
        $location['children'] = array();
        $location['location'] = array();

        $parent_immediate = array();

        $results = Query_helper::get_info($CI->config->item('table_ams_setup_locations'), array('*'), array(), 0, 0, array('ordering ASC'));
        foreach ($results as $result)
        {
            $location['location'][$result['id']] = $result;
            $location['children'][$result['parent']][] = $result['id'];

            $parent_immediate[$result['id']] = $result['parent']; // Generate Immediate Parent Array
        }
        foreach ($results as $result)
        {
            Category_helper::get_location_parents($result['id'], $parent_immediate, $location);
        }
        return $location;
    }

    public static function get_location_parents($location_id, $parent_immediate, &$locations)
    {
        $current_id = $location_id;
        do
        {
            $locations['parents'][$location_id][] = $parent_immediate[$current_id];
            $current_id = $parent_immediate[$current_id];
        } while ($current_id != 0);
    }

    public static function get_location_tree_list($id = 0) //Sub Locations Dropdown list
    {
        $CI =& get_instance();
        if ($id > 0)
        {
            $CI->db->where('id !=', $id);
        }
        else
        {
            $CI->db->where('status', $CI->config->item('system_status_active'));
        }
        $CI->db->order_by('ordering');
        $results = $CI->db->get($CI->config->item('table_ams_setup_locations'))->result_array();


        $children = array();
        foreach ($results as &$result)
        {
            if ($result['status'] == $CI->config->item('system_status_inactive'))
            {
                $result['name'] .= ' (' . $result['status'] . ')';
            }
            $children[$result['parent']]['ids'][$result['id']] = $result['id'];
            $children[$result['parent']]['children'][$result['id']] = $result;
        }

        $tree = array();
        if (isset($children[0]))
        {
            $level_0 = $children[0]['children'];
            foreach ($level_0 as $row)
            {
                Category_helper::get_sub_tree_list_for_dropdown($row, '', $tree, $children);
            }
        }
        return $tree;
    }

    // Recursion method for Tree List Dropdown in ADD & EDIT (for both 'Category' & 'Location' setup)
    public static function get_sub_tree_list_for_dropdown($row, $prefix, &$tree, $children)
    {
        $row['prefix'] = $prefix;
        $tree[] = $row;

        $subs = array();
        if (isset($children[$row['id']]))
        {
            $subs = $children[$row['id']]['children'];
        }
        if (sizeof($subs) > 0)
        {
            foreach ($subs as $sub)
            {
                Category_helper::get_sub_tree_list_for_dropdown($sub, $prefix . '- ', $tree, $children);
            }
        }
    }
}
