<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Setup_category extends Root_Controller
{
    public $message;
    public $permissions;
    public $controller_url;
    public $prefix_length;

    public function __construct()
    {
        parent::__construct();
        $this->message = "";
        $this->permissions = User_helper::get_permission(get_class($this));
        $this->controller_url = strtolower(get_class($this));
        $this->load->helper('category');
        $this->prefix_length = 3;
        // Extra Language
        $this->lang->language['LABEL_PREFIX'] = 'Prefix';
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
        elseif ($action == "tree_view")
        {
            $this->system_tree_view();
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
                    $data['category_' . $i] = 1;
                }
            }
            $data['ordering'] = 1;
            $data['status'] = 1;
        }
        return $data;
    }

    private function system_list()
    {
        if (isset($this->permissions['action0']) && ($this->permissions['action0'] == 1))
        {
            $user = User_helper::get_user();
            $method = 'list';

            $data = array();
            $category = Category_helper::get_category_parent_children();
            $max_parent_length = 0;
            foreach ($category['parents'] as $parent)
            {
                $length = sizeof($parent);
                if ($length > $max_parent_length)
                {
                    $max_parent_length = $length;
                }
            }
            $data['max_parent_length'] = $max_parent_length;

            //Dynamic language for Sub Categories
            for ($i = 1; $i <= $max_parent_length; $i++)
            {
                $this->lang->language['LABEL_CATEGORY_' . $i] = 'Category ' . $i;
            }

            $data['system_preference_items'] = System_helper::get_preference($user->user_id, $this->controller_url, $method, $this->get_preference_headers($method, $max_parent_length));
            $data['title'] = "Category List";
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

        $category = Category_helper::get_category_parent_children();
        foreach ($category['parents'] as $category_id => $parent_ids)
        {
            $length_actual = sizeof($parent_ids);
            $length = ($length_actual - 2);
            $index = 1;

            $item = array(
                'id' => $category['category'][$category_id]['id'],
                'ordering' => $category['category'][$category_id]['ordering'],
                'status' => $category['category'][$category_id]['status']
            );
            for ($i = $length; $i >= 0; $i--)
            {
                $item['category_' . ($index++)] = $category['category'][$parent_ids[$i]]['name'];
            }
            $item['category_' . ($index++)] = $category['category'][$category_id]['name'];

            while ($index <= $max_parent_length)
            {
                $item['category_' . ($index++)] = "";
            }

            $items[] = $item;
        }
        $this->json_return($items);
    }

    private function system_add()
    {
        if (isset($this->permissions['action1']) && ($this->permissions['action1'] == 1))
        {
            $data = array();
            $data['item'] = Array(
                'id' => 0,
                'name' => '',
                'prefix' => '',
                'parent' => 0,
                'ordering' => 99,
                'status' => $this->config->item('system_status_active')
            );

            $data['categories'] = Category_helper::get_category_tree_list();
            $data['title'] = "Create New Category";
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
            $data['item'] = Query_helper::get_info($this->config->item('table_ams_setup_categories'), array('*'), array('id =' . $item_id), 1, 0, array('id ASC'));
            if (!$data['item'])
            {
                System_helper::invalid_try(__FUNCTION__, $item_id, 'Edit Not Exists');
                $ajax['status'] = false;
                $ajax['system_message'] = 'Invalid Try.';
                $this->json_return($ajax);
            }

            $data['categories'] = Category_helper::get_category_tree_list($item_id);
            $data['title'] = "Edit Category :: " . $data['item']['name'];
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

            $result = Query_helper::get_info($this->config->item('table_ams_setup_categories'), '*', array('id =' . $id), 1);
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
            $this->db->set('revision_count', 'revision_count+1', FALSE);
            Query_helper::update($this->config->item('table_ams_setup_categories'), $item, array('id=' . $id));
        }
        else // ADD
        {
            $item['revision_count'] = 1;
            $item['date_created'] = $time;
            $item['user_created'] = $user->user_id;
            Query_helper::add($this->config->item('table_ams_setup_categories'), $item);
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

    private function system_tree_view()
    {
        if (isset($this->permissions['action0']) && ($this->permissions['action0'] == 1))
        {
            $results = Query_helper::get_info($this->config->item('table_ams_setup_categories'), array('*'), array(), 0, 0, array('parent ASC', 'ordering ASC'));
            $data = array();
            $data['categories'] = array();
            foreach ($results as $result)
            {
                $data['categories'][] = array(
                    'id' => $result['id'],
                    'name' => $result['name'],
                    'ordering' => $result['ordering'],
                    'parent' => ($result['parent'] > 0) ? $result['parent'] : '',
                    'status' => ($result['status'] == $this->config->item('system_status_inactive')) ? '<br/><span>(' . $result['status'] . ')<span>' : ''
                );
            }

            $data['title'] = "Category Tree View";
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

    private function check_validation()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('item[name]', $this->lang->line('LABEL_NAME'), 'required|trim');
        $this->form_validation->set_rules('item[prefix]', $this->lang->line('LABEL_PREFIX'), 'required|trim|alpha|exact_length['.$this->prefix_length.']');
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
