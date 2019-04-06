<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Setup_location extends Root_Controller
{
    public $message;
    public $permissions;
    public $controller_url;

    public function __construct()
    {
        parent::__construct();
        $this->message = "";
        $this->permissions = User_helper::get_permission(get_class($this));
        $this->controller_url = strtolower(get_class($this));
        $this->load->helper('category');
    }

    public function index($action = "list", $id = 0)
    {
        if ($action == "list")
        {
            $this->system_list();
        }
        elseif ($action == "get_items")
        {
            $this->system_get_items();
        }
        elseif ($action == "tree_view")
        {
            $this->system_tree_view();
        }
        elseif ($action == "add")
        {
            $this->system_add();
        }
        elseif ($action == "edit")
        {
            $this->system_edit($id);
        }
        elseif ($action == "save")
        {
            $this->system_save();
        }
        elseif ($action == "set_preference_list")
        {
            $this->system_set_preference('list');
        }
        elseif ($action == "save_preference")
        {
            System_helper::save_preference();
        }
        else
        {
            $this->system_list();
        }
    }

    private function get_preference_headers($method, $length = 0)
    {
        $data = array();
        if ($method == 'list')
        {
            $data['id'] = 1;
            if ($length > 0)
            {
                for ($i = 1; $i <= $length; $i++)
                {
                    $data['location_' . $i] = 1;
                }
            }
            $data['ordering'] = 1;
            $data['status'] = 1;
        }
        return $data;
    }

    private function system_set_preference($method = 'list')
    {
        $user = User_helper::get_user();
        if (isset($this->permissions['action6']) && ($this->permissions['action6'] == 1))
        {
            $data['system_preference_items'] = System_helper::get_preference($user->user_id, $this->controller_url, $method, $this->get_preference_headers($method));
            $data['preference_method_name'] = $method;
            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view("preference_add_edit", $data, true));
            $ajax['system_page_url'] = site_url($this->controller_url . '/index/set_preference_' . $method);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }

    private function system_list()
    {
        if (isset($this->permissions['action0']) && ($this->permissions['action0'] == 1))
        {
            $user = User_helper::get_user();
            $method = 'list';

            $data = array();
            $location = Category_helper::get_location_parent_children();
            $max_parent_length = 0;
            foreach ($location['parents'] as $parent)
            {
                $length = sizeof($parent);
                if ($length > $max_parent_length)
                {
                    $max_parent_length = $length;
                }
            }
            $data['max_parent_length'] = $max_parent_length;

            //Dynamic language for Sub Locations
            for ($i = 1; $i <= $max_parent_length; $i++)
            {
                $this->lang->language['LABEL_LOCATION_' . $i] = 'Location ' . $i;
            }

            $data['system_preference_items'] = System_helper::get_preference($user->user_id, $this->controller_url, $method, $this->get_preference_headers($method, $max_parent_length));
            $data['title'] = "Location List";
            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view($this->controller_url . "/list", $data, true));
            if ($this->message)
            {
                $ajax['system_message'] = $this->message;
            }
            $ajax['system_page_url'] = site_url($this->controller_url);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }

    private function system_get_items()
    {
        $max_parent_length = $this->input->post('max_parent_length');
        $items = array();

        $location = Category_helper::get_location_parent_children();
        foreach ($location['parents'] as $location_id => $parent_ids) // $parent_ids[$i]
        {
            $length_actual = sizeof($parent_ids);
            $length = ($length_actual - 2);
            $index = 1;

            $arr = array(
                'id' => $location['location'][$location_id]['id'],
                'ordering' => $location['location'][$location_id]['ordering'],
                'status' => $location['location'][$location_id]['status']
            );
            for ($i = $length; $i >= 0; $i--)
            {
                $arr['location_' . ($index++)] = $location['location'][$parent_ids[$i]]['name'];
            }
            $arr['location_' . ($index++)] = $location['location'][$location_id]['name'];

            while ($index <= $max_parent_length)
            {
                $arr['location_' . ($index++)] = "";
            }

            $items[] = $arr;
        }
        $this->json_return($items);
    }

    private function system_tree_view()
    {
        if (isset($this->permissions['action1']) && ($this->permissions['action1'] == 1))
        {
            $results = Query_helper::get_info($this->config->item('table_ams_setup_locations'), array('*'), array(), 0, 0, array('parent ASC', 'ordering ASC'));
            $data = array();
            $data['locations'] = array();
            foreach ($results as $result)
            {
                $data['locations'][] = array(
                    'id' => $result['id'],
                    'name' => $result['name'],
                    'ordering' => $result['ordering'],
                    'parent' => ($result['parent'] > 0) ? $result['parent'] : ""
                );
            }

            $data['title'] = "Location Tree View";
            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view($this->controller_url . "/tree_view", $data, true));
            if ($this->message)
            {
                $ajax['system_message'] = $this->message;
            }
            $ajax['system_page_url'] = site_url($this->controller_url . "/index/tree_view");
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }

    private function system_add()
    {
        if (isset($this->permissions['action1']) && ($this->permissions['action1'] == 1))
        {
            $data = array();
            $data['item'] = Array(
                'id' => 0,
                'name' => '',
                'parent' => 0,
                'ordering' => 99,
                'status' => $this->config->item('system_status_active')
            );

            $data['locations'] = Query_helper::get_info($this->config->item('table_ams_setup_locations'), '*', array('status != "' . $this->config->item('system_status_delete') . '"'));

            $data['title'] = "Create New Location";
            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view($this->controller_url . "/add_edit", $data, true));
            if ($this->message)
            {
                $ajax['system_message'] = $this->message;
            }
            $ajax['system_page_url'] = site_url($this->controller_url . "/index/add");
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }

    private function system_edit($id)
    {
        if (isset($this->permissions['action2']) && ($this->permissions['action2'] == 1))
        {
            if ($id > 0)
            {
                $item_id = $id;
            }
            else
            {
                $item_id = $this->input->post('id');
            }
            $data = array();
            $data['item'] = Query_helper::get_info($this->config->item('table_ams_setup_locations'), array('*'), array('id =' . $item_id, 'status !="' . $this->config->item('system_status_delete') . '"'), 1, 0, array('id ASC'));
            if (!$data['item'])
            {
                System_helper::invalid_try(__FUNCTION__, $item_id, 'Edit Not Exists');
                $ajax['status'] = false;
                $ajax['system_message'] = 'Invalid Try.';
                $this->json_return($ajax);
            }
            $data['locations'] = Query_helper::get_info($this->config->item('table_ams_setup_locations'), '*', array('status != "' . $this->config->item('system_status_delete') . '"'));

            $data['title'] = "Edit Location :: " . $data['item']['name'];
            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view($this->controller_url . "/add_edit", $data, true));
            if ($this->message)
            {
                $ajax['system_message'] = $this->message;
            }
            $ajax['system_page_url'] = site_url($this->controller_url . '/index/edit/' . $item_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }

    private function system_save()
    {
        $id = $this->input->post('id');
        $item = $this->input->post('item');
        $user = User_helper::get_user();
        $time = time();

        // Validation Checking
        if ($id > 0) // EDIT
        {
            if (!(isset($this->permissions['action2']) && ($this->permissions['action2'] == 1)))
            {
                $ajax['status'] = false;
                $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
            }

            $result = Query_helper::get_info($this->config->item('table_ams_setup_locations'), '*', array('id =' . $id, 'status != "' . $this->config->item('system_status_delete') . '"'), 1);
            if (!$result)
            {
                System_helper::invalid_try(__FUNCTION__, $id, 'Update Not Exists');
                $ajax['status'] = false;
                $ajax['system_message'] = 'Invalid Try.';
                $this->json_return($ajax);
            }
        }
        else // ADD
        {
            if (!(isset($this->permissions['action1']) && ($this->permissions['action1'] == 1)))
            {
                $ajax['status'] = false;
                $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
            }
        }

        if (!$this->check_validation())
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->message;
            $this->json_return($ajax);
        }

        $this->db->trans_start(); //DB Transaction Handle START
        if ($id > 0) // EDIT
        {
            $item['date_updated'] = $time;
            $item['user_updated'] = $user->user_id;
            Query_helper::update($this->config->item('table_ams_setup_locations'), $item, array('id=' . $id));
        }
        else // ADD
        {
            $item['date_created'] = $time;
            $item['user_created'] = $user->user_id;
            Query_helper::add($this->config->item('table_ams_setup_locations'), $item);
        }
        $this->db->trans_complete(); //DB Transaction Handle END

        if ($this->db->trans_status() === TRUE)
        {
            $save_and_new = $this->input->post('system_save_new_status');
            $this->message = $this->lang->line("MSG_SAVED_SUCCESS");
            if ($save_and_new == 1)
            {
                $this->system_add();
            }
            else
            {
                $this->system_list();
            }
        }
        else
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("MSG_SAVED_FAIL");
            $this->json_return($ajax);
        }
    }

    private function check_validation()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('item[name]', $this->lang->line('LABEL_NAME'), 'required|trim');
        $this->form_validation->set_rules('item[ordering]', $this->lang->line('LABEL_ORDER'), 'required');
        $this->form_validation->set_rules('item[status]', $this->lang->line('LABEL_STATUS'), 'required');
        if ($this->form_validation->run() == FALSE)
        {
            $this->message = validation_errors();
            return false;
        }
        return true;
    }
}
