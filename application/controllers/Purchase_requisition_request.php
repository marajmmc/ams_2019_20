<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Purchase_requisition_request extends Root_Controller
{
    public $message;
    public $permissions;
    public $controller_url;
    public $common_view_location;

    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions=User_helper::get_permission(get_class());
        $this->controller_url=strtolower(get_class());
        $this->common_view_location='purchase_requisition_request';
        $this->load->helper('category');
        $this->load->helper('ams');
        $this->language_labels();
    }
    private function language_labels()
    {
        $this->lang->language['LABEL_DATE_REQUISITION']='Date';
        $this->lang->language['LABEL_CATEGORY_NAME']='Category';
        $this->lang->language['LABEL_AMOUNT_PRICE_UNIT']='Unit Price';
        $this->lang->language['LABEL_AMOUNT_PRICE_TOTAL']='Total Price';
        $this->lang->language['LABEL_REASON']='Reason';
        $this->lang->language['LABEL_SPECIFICATION']='Specification';
        $this->lang->language['LABEL_REVISION_COUNT_REQUEST']='Number of Edit';
        $this->lang->language['LABEL_ITEMS']='Add More Items';
        $this->lang->language['LABEL_STATUS_FORWARD']='Forward Status';
        $this->lang->language['LABEL_STATUS_APPROVE']='Approve Status';
    }
    public function index($action="list",$id=0)
    {
        if($action=="list")
        {
            $this->system_list();
        }
        elseif($action=="get_items")
        {
            $this->system_get_items();
        }
        elseif($action=="list_all")
        {
            $this->system_list_all();
        }
        elseif($action=="get_items_all")
        {
            $this->system_get_items_all();
        }
        elseif($action=="add")
        {
            $this->system_add();
        }
        elseif($action=="edit")
        {
            $this->system_edit($id);
        }
        elseif($action=="save")
        {
            $this->system_save();
        }
        elseif($action=="forward")
        {
            $this->system_forward($id);
        }
        elseif($action=="save_forward")
        {
            $this->system_save_forward();
        }
        elseif($action=="set_preference")
        {
            $this->system_set_preference('list');
        }
        elseif($action=="set_preference_all")
        {
            $this->system_set_preference('list_all');
        }
        elseif($action=="save_preference")
        {
            System_helper::save_preference();
        }
        else
        {
            $this->system_list();
        }
    }
    private function get_preference_headers($method)
    {
        $data=array();
        if($method=='list')
        {
            $data['id']= 1;
            $data['date_requisition']= 1;
            $data['supplier_name']= 1;
            $data['category_name']= 1;
            $data['quantity_total']= 1;
            $data['amount_price_unit']= 1;
            $data['amount_price_total']= 1;
            $data['specification']= 1;
            $data['reason']= 1;
            $data['remarks']= 1;
            $data['revision_count_request']= 1;
        }
        else if($method=='list_all')
        {
            $data['id']= 1;
            $data['date_requisition']= 1;
            $data['supplier_name']= 1;
            $data['category_name']= 1;
            $data['quantity_total']= 1;
            $data['amount_price_unit']= 1;
            $data['amount_price_total']= 1;
            $data['specification']= 1;
            $data['reason']= 1;
            $data['remarks']= 1;
            $data['revision_count_request']= 1;
            $data['status']= 1;
            $data['status_forward']= 1;
            $data['status_approve']= 1;
        }
        else
        {

        }

        return $data;
    }
    private function system_set_preference($method)
    {
        $user = User_helper::get_user();
        if(isset($this->permissions['action6']) && ($this->permissions['action6']==1))
        {
            $data['system_preference_items']=System_helper::get_preference($user->user_id,$this->controller_url,$method,$this->get_preference_headers($method));
            $data['preference_method_name']=$method;
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("preference_add_edit",$data,true));
            $ajax['system_page_url']=site_url($this->controller_url.'/index/set_preference_'.$method);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_list()
    {
        $user = User_helper::get_user();
        $method='list';
        if(isset($this->permissions['action0'])&&($this->permissions['action0']==1))
        {
            $data['system_preference_items']= System_helper::get_preference($user->user_id, $this->controller_url, $method, $this->get_preference_headers($method));
            $data['title']="Purchase Order Pending List";
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/list",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_get_items()
    {
        $this->db->from($this->config->item('table_ams_requisition_request').' item');
        $this->db->select('item.*, category.name category_name');

        $this->db->join($this->config->item('table_ams_setup_categories').' category','category.id=item.category_id','INNER');
        $this->db->join($this->config->item('table_ams_setup_suppliers').' supplier','supplier.id=item.supplier_id','LEFT');
        $this->db->select('supplier.name supplier_name');

        $this->db->where('item.status',$this->config->item('system_status_active'));
        $this->db->where('item.status_forward',$this->config->item('system_status_pending'));
        $this->db->order_by('item.id','DESC');
        $items=$this->db->get()->result_array();
        foreach($items as &$item)
        {
            $item['date_requisition']=System_helper::display_date($item['date_requisition']);
        }
        $this->json_return($items);
    }
    private function system_list_all()
    {
        $user = User_helper::get_user();
        $method='list_all';
        if(isset($this->permissions['action0'])&&($this->permissions['action0']==1))
        {
            $data['system_preference_items']= System_helper::get_preference($user->user_id, $this->controller_url, $method, $this->get_preference_headers($method));
            $data['title']="Purchase Order All List";
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/list_all",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/list_all');
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_get_items_all()
    {
        $this->db->from($this->config->item('table_ams_requisition_request').' item');
        $this->db->select('item.*, category.name category_name');

        $this->db->join($this->config->item('table_ams_setup_categories').' category','category.id=item.category_id','INNER');
        $this->db->join($this->config->item('table_ams_setup_suppliers').' supplier','supplier.id=item.supplier_id','LEFT');
        $this->db->select('supplier.name supplier_name');

        $this->db->where('item.status !=',$this->config->item('system_status_delete'));
        $this->db->order_by('item.id','DESC');
        $items=$this->db->get()->result_array();
        foreach($items as &$item)
        {
            $item['date_requisition']=System_helper::display_date($item['date_requisition']);
        }
        $this->json_return($items);
    }
    private function system_add()
    {
        if(isset($this->permissions['action1'])&&($this->permissions['action1']==1))
        {
            $user=User_helper::get_user();
            $data['title']="Create New Requisition";
            $data['item']['id']=0;
            $data['item']['date_requisition']=time();
            $data['item']['supplier_id']='';
            $data['item']['quantity_total']=1;
            $data['item']['amount_price_unit']='';
            $data['item']['amount_price_total']=0;
            $data['item']['specification']='';
            $data['item']['reason']='';
            $data['item']['remarks']='';
            $data['categories']=$this->get_parent_wise_task();
            $data['suppliers']=Query_helper::get_info($this->config->item('table_ams_setup_suppliers'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'),0,0,array('ordering ASC'));
            $data['responsible_user_groups']=Query_helper::get_info($this->config->item('table_ams_setup_responsible_user_group'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"', "user_ids like '%,$user->user_id,%'"),0,0,array('ordering ASC'));
            if(!(sizeof($data['responsible_user_groups'])>0))
            {
                $ajax['status']=false;
                $ajax['system_message']='You are not assign to responsible user group.';
                $this->json_return($ajax);
            }

            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/add_edit",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/add');
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_edit($id)
    {
        if(isset($this->permissions['action2'])&&($this->permissions['action2']==1))
        {
            if($id>0)
            {
                $item_id=$id;
            }
            else
            {
                $item_id=$this->input->post('id');
            }

            $data['item']=Query_helper::get_info($this->config->item('table_ams_requisition_request'),array('*'),array('id ='.$item_id),1,0,array('id ASC'));
            if(!$data['item'])
            {
                System_helper::invalid_try('Edit Non Exists',$item_id);
                $ajax['status']=false;
                $ajax['system_message']='Invalid Requisition.';
                $this->json_return($ajax);
            }
            if($data['item']['status']==$this->config->item('system_status_delete'))
            {
                $ajax['status']=false;
                $ajax['system_message']='Purchase Order deleted.';
                $this->json_return($ajax);
            }
            if($data['item']['status_forward']==$this->config->item('system_status_forwarded'))
            {
                $ajax['status']=false;
                $ajax['system_message']='Purchase Order already forwarded.';
                $this->json_return($ajax);
            }
            if($data['item']['status_approve']==$this->config->item('system_status_rejected'))
            {
                $ajax['status']=false;
                $ajax['system_message']='Purchase Order already rejected.';
                $this->json_return($ajax);
            }
            $data['categories']=$this->get_parent_wise_task();
            $data['suppliers']=Query_helper::get_info($this->config->item('table_ams_setup_suppliers'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'),0,0,array('ordering ASC'));

            $data['title']="Edit Purchase Order :: ". $data['item']['id'];
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/add_edit",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/edit/'.$item_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_save()
    {
        $id = $this->input->post("id");
        $user = User_helper::get_user();
        $time=time();
        $item_head=$this->input->post('item');
        $categories=$this->input->post('categories');
        $category_id=0;
        foreach($categories['parent_id'] as $key=>$value)
        {
            if($value)
            {
                $category_id=$value;
            }

        }
        if(!$category_id)
        {
            $ajax['status']=false;
            $ajax['system_message']='Category is required.';
            $this->json_return($ajax);
        }
        if($id>0)
        {
            if(!(isset($this->permissions['action2']) && ($this->permissions['action2']==1)))
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
            }
            $result=Query_helper::get_info($this->config->item('table_ams_requisition_request'),'*',array('id ='.$id),1);
            if(!$result)
            {
                System_helper::invalid_try('Update Non Exists',$id);
                $ajax['status']=false;
                $ajax['system_message']='Invalid Requisition.';
                $this->json_return($ajax);
            }
            if($result['status']==$this->config->item('system_status_delete'))
            {
                $ajax['status']=false;
                $ajax['system_message']='Purchase Order deleted.';
                $this->json_return($ajax);
            }
            if($result['status_forward']==$this->config->item('system_status_forwarded'))
            {
                $ajax['status']=false;
                $ajax['system_message']='Purchase Order already forwarded.';
                $this->json_return($ajax);
            }
            if($result['status_approve']==$this->config->item('system_status_rejected'))
            {
                $ajax['status']=false;
                $ajax['system_message']='Purchase Order already rejected.';
                $this->json_return($ajax);
            }
        }
        else
        {
            if(!(isset($this->permissions['action1']) && ($this->permissions['action1']==1)))
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
            }
        }
        if(!$this->check_validation())
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->message;
            $this->json_return($ajax);
        }

        $this->db->trans_start();  //DB Transaction Handle START

        if($id>0)
        {
            //$data=array();
            $item_head['date_requisition']=System_helper::get_time($item_head['date_requisition']);
            $item_head['category_id']=$category_id;
            $item_head['amount_price_total']=($item_head['quantity_total']*$item_head['amount_price_unit']);
            $item_head['date_updated'] = $time;
            $item_head['user_updated'] = $user->user_id;
            $this->db->set('revision_count_request', 'revision_count_request+1', FALSE);
            Query_helper::update($this->config->item('table_ams_requisition_request'),$item_head, array('id='.$id), false);
        }
        else
        {
            $item_head['date_requisition']=System_helper::get_time($item_head['date_requisition']);
            $item_head['category_id']=$category_id;
            $item_head['amount_price_total']=($item_head['quantity_total']*$item_head['amount_price_unit']);
            $item_head['date_created']=$time;
            $item_head['user_created']=$user->user_id;
            $item_head['revision_count_request']=1;
            Query_helper::add($this->config->item('table_ams_requisition_request'),$item_head, false);
        }

        $this->db->trans_complete();   //DB Transaction Handle END
        if ($this->db->trans_status() === TRUE)
        {
            $save_and_new=$this->input->post('system_save_new_status');
            $this->message=$this->lang->line("MSG_SAVED_SUCCESS");
            if($save_and_new==1)
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
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("MSG_SAVED_FAIL");
            $this->json_return($ajax);
        }
    }
    private function system_forward($id)
    {
        if(isset($this->permissions['action7'])&&($this->permissions['action7']==1))
        {
            if($id>0)
            {
                $item_id=$id;
            }
            else
            {
                $item_id=$this->input->post('id');
            }

            //$data['item']=Query_helper::get_info($this->config->item('table_ams_requisition_request'),array('*'),array('id ='.$item_id),1,0,array('id ASC'));
            $this->db->from($this->config->item('table_ams_requisition_request').' item');
            $this->db->select('item.*, category.name category_name');

            $this->db->join($this->config->item('table_ams_setup_categories').' category','category.id=item.category_id','INNER');
            $this->db->join($this->config->item('table_ams_setup_suppliers').' supplier','supplier.id=item.supplier_id','LEFT');
            $this->db->select('supplier.name supplier_name');

            $this->db->where('item.status !=',$this->config->item('system_status_delete'));
            $this->db->where('item.id',$item_id);
            $data['item']=$this->db->get()->row_array();
            if(!$data['item'])
            {
                System_helper::invalid_try('Forward Non Exists',$item_id);
                $ajax['status']=false;
                $ajax['system_message']='Invalid Requisition.';
                $this->json_return($ajax);
            }
            if($data['item']['status']==$this->config->item('system_status_delete'))
            {
                $ajax['status']=false;
                $ajax['system_message']='Purchase Order deleted.';
                $this->json_return($ajax);
            }
            if($data['item']['status_forward']==$this->config->item('system_status_forwarded'))
            {
                $ajax['status']=false;
                $ajax['system_message']='Purchase Order already forwarded.';
                $this->json_return($ajax);
            }
            if($data['item']['status_approve']==$this->config->item('system_status_rejected'))
            {
                $ajax['status']=false;
                $ajax['system_message']='Purchase Order already rejected.';
                $this->json_return($ajax);
            }
            $data['categories']=$this->get_parent_wise_task();
            $data['info_basic']=Ams_helper::get_basic_info($data['item']);

            $data['title']="Purchase Order Forward";
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/forward",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/forward/'.$item_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_save_forward()
    {
        $id = $this->input->post("id");
        $user = User_helper::get_user();
        $time=time();
        $item_head=$this->input->post('item');
        if($id>0)
        {
            if(!((isset($this->permissions['action7']) && ($this->permissions['action7']==1))))
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
            }
            if($item_head['status_forward']!=$this->config->item('system_status_forwarded'))
            {
                $ajax['status']=false;
                $ajax['system_message']='Forward Field is required.';
                $this->json_return($ajax);
            }
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
        $this->db->trans_start();  //DB Transaction Handle START

        $data=array();

        $data['date_forwarded']=$time;
        $data['user_forwarded']=$user->user_id;
        $data['status_forward']=$item_head['status_forward'];
        $this->db->set('revision_count_forwarded', 'revision_count_forwarded+1', FALSE);
        Query_helper::update($this->config->item('table_ams_requisition_request'),$data,array('id='.$id));

        $this->db->trans_complete();   //DB Transaction Handle END

        if ($this->db->trans_status() === TRUE)
        {
            $this->message=$this->lang->line("MSG_SAVED_SUCCESS");
            $this->system_list();
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("MSG_SAVED_FAIL");
            $this->json_return($ajax);
        }
    }
    private function check_validation()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('item[quantity_total]',$this->lang->line('LABEL_QUANTITY_TOTAL'),'required');
        $this->form_validation->set_rules('item[amount_price_unit]',$this->lang->line('LABEL_AMOUNT_PRICE_UNIT'),'required');
        $this->form_validation->set_rules('item[specification]',$this->lang->line('LABEL_SPECIFICATION'),'required');
        $this->form_validation->set_rules('item[reason]',$this->lang->line('LABEL_REASON'),'required');
        if($this->form_validation->run() == FALSE)
        {
            $this->message=validation_errors();
            return false;
        }
        return true;
    }
    public function get_parent_wise_task()
    {
        $this->db->from($this->config->item('table_ams_setup_categories'));
        $this->db->order_by('ordering');
        $results=$this->db->get()->result_array();
        $parents=array();
        foreach($results as $result)
        {
            //$parents[$result['parent']][$result['id']]['value']=$result['id'];
            $parents[$result['parent']][$result['id']]=$result['name'];
        }
        return json_encode($parents);
    }
}
