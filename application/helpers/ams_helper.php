<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ams_helper
{
    public static function get_basic_info($result)
    {
        $CI = & get_instance();
        //--------- System User Info ------------
        $user_ids=array();
        $user_ids[$result['user_created']]=$result['user_created'];
        if($result['user_updated']>0)
        {
            $user_ids[$result['user_updated']]=$result['user_updated'];
        }
        if($result['user_forwarded']>0)
        {
            $user_ids[$result['user_forwarded']]=$result['user_forwarded'];
        }
        if($result['user_approved']>0)
        {
            $user_ids[$result['user_approved']]=$result['user_approved'];
        }
        $user_info = System_helper::get_users_info($user_ids);

        //---------------- Basic Info ----------------
        $data = array();
        $data[] = array
        (
            'label_1' => $CI->lang->line('LABEL_DATE'),
            'value_1' => System_helper::display_date($result['date_requisition']),
            'label_2' => 'Revision (Edit)',
            'value_2' => $result['revision_count_request'],
        );
        $data[] = array
        (
            'label_1' => 'Created By',
            'value_1' => $user_info[$result['user_created']]['name'] . ' ( ' . $user_info[$result['user_created']]['employee_id'] . ' )',
            'label_2' => 'Created Time',
            'value_2' => System_helper::display_date_time($result['date_created'])
        );
        if($result['user_updated']>0)
        {
            $inactive_update_by='Updated By';
            $inactive_update_time='Updated Time';
            if($result['status']==$CI->config->item('system_status_rejected'))
            {
                $inactive_update_by='Reject By';
                $inactive_update_time='Reject Time';
            }
            $data[] = array
            (
                'label_1' => $inactive_update_by,
                'value_1' => $user_info[$result['user_updated']]['name'] . ' ( ' . $user_info[$result['user_updated']]['employee_id'] . ' )',
                'label_2' => $inactive_update_time,
                'value_2' => System_helper::display_date_time($result['date_updated'])
            );
        }
        $data[] = array
        (
            'label_1' => $CI->lang->line('LABEL_STATUS_FORWARD'),
            'value_1' => $result['status_forward'],
            'label_2' => 'Revision (Forward)',
            'value_2' => $result['revision_count_forwarded'],
        );
        if($result['status_forward']==$CI->config->item('system_status_forwarded'))
        {
            $data[] = array
            (
                'label_1' => 'Forwarded By',
                'value_1' => $user_info[$result['user_forwarded']]['name'] . ' ( ' . $user_info[$result['user_forwarded']]['employee_id'] . ' )',
                'label_2' => 'Forwarded Time',
                'value_2' => System_helper::display_date_time($result['date_forwarded'])
            );
        }
        if($result['status_approve']==$CI->config->item('system_status_approved'))
        {
            $label_approve=$CI->config->item('system_status_approved');
        }
        else if($result['status_approve']==$CI->config->item('system_status_rejected'))
        {
            $label_approve='Reject';
        }
        else if($result['status_approve']==$CI->config->item('system_status_rollback'))
        {
            $label_approve=$CI->config->item('system_status_approved');
        }
        else
        {
            $label_approve=$CI->config->item('system_status_approved');
        }
        $data[] = array
        (
            'label_1' => $label_approve.' Status',
            'value_1' => $result['status_approve'],
            'label_2' => 'Revision ('.$label_approve.')',
            'value_2' => $result['revision_count_approved'],
        );
        if($result['status_approve']!=$CI->config->item('system_status_pending'))
        {
            $data[] = array
            (
                'label_1' => $label_approve.' By',
                'value_1' => $user_info[$result['user_approved']]['name'] . ' ( ' . $user_info[$result['user_approved']]['employee_id'] . ' )',
                'label_2' => $label_approve.' Time',
                'value_2' => System_helper::display_date_time($result['date_approved'])
            );
            $data[] = array
            (
                'label_1' => $label_approve.' Remarks',
                'value_1' => $result['remarks_approve']
            );
        }
        if($result['revision_count_rollback']>0)
        {
            $data[] = array
            (
                'label_1' => 'Revision (Rollback)',
                'value_1' => $result['revision_count_rollback']
            );
        }
        return $data;
    }

    public static function get_child_ids_designation($designation_id)
    {
        $CI =& get_instance();
        $CI->db->from($CI->config->item('table_login_setup_designation'));
        $CI->db->order_by('ordering');
        $results = $CI->db->get()->result_array();
        $child_ids[0] = 0;
        $parents = array();
        foreach ($results as $result)
        {
            $parents[$result['parent']][] = $result;
        }
        Ams_helper::get_sub_child_ids_designation($designation_id, $parents, $child_ids);
        return $child_ids;
    }
    public static function get_sub_child_ids_designation($id, $parents, &$child_ids)
    {
        if (isset($parents[$id]))
        {
            foreach ($parents[$id] as $child)
            {
                $child_ids[$child['id']] = $child['id'];
                if (isset($parents[$child['id']]) && sizeof($parents[$child['id']]) > 0)
                {
                    Ams_helper::get_sub_child_ids_designation($child['id'], $parents, $child_ids);
                }
            }
        }
    }
}
