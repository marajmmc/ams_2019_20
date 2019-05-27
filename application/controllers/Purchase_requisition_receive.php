<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Purchase_requisition_receive extends Root_Controller
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
        //$this->lang->language['LABEL_DATE_REQUISITION']='Date';
        $this->lang->language['LABEL_CATEGORY_NAME']='Category';
        //$this->lang->language['LABEL_MODEL_NUMBER']='Asset Name';
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
        elseif($action=="receive")
        {
            $this->system_receive($id);
        }
        elseif($action=="save_receive")
        {
            $this->system_save_receive();
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
            //$data['date_requisition']= 1;
            $data['supplier_name']= 1;
            $data['category_name']= 1;
            //$data['model_number']= 1;
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
            //$data['date_requisition']= 1;
            $data['supplier_name']= 1;
            $data['category_name']= 1;
            //$data['model_number']= 1;
            $data['quantity_total']= 1;
            $data['amount_price_unit']= 1;
            $data['amount_price_total']= 1;
            $data['specification']= 1;
            $data['reason']= 1;
            $data['remarks']= 1;
            $data['revision_count_request']= 1;
            $data['status']= 1;
            $data['status_receive']= 1;
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
            $data['title']="Purchase Order Receive List";
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
        $this->db->where('item.status_approve',$this->config->item('system_status_approved'));
        $this->db->where('item.status_receive',$this->config->item('system_status_pending'));
        $this->db->order_by('item.id','DESC');
        $items=$this->db->get()->result_array();
        /*foreach($items as &$item)
        {
            $item['date_requisition']=System_helper::display_date($item['date_requisition']);
        }*/
        $this->json_return($items);
    }
    private function system_list_all()
    {
        $user = User_helper::get_user();
        $method='list_all';
        if(isset($this->permissions['action0'])&&($this->permissions['action0']==1))
        {
            $data['system_preference_items']= System_helper::get_preference($user->user_id, $this->controller_url, $method, $this->get_preference_headers($method));
            $data['title']="Purchase Order Receive All List";
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
        $this->db->where('item.status_approve',$this->config->item('system_status_approved'));
        $this->db->order_by('item.id','DESC');
        $items=$this->db->get()->result_array();
        /*foreach($items as &$item)
        {
            $item['date_requisition']=System_helper::display_date($item['date_requisition']);
        }*/
        $this->json_return($items);
    }
    private function system_receive($id)
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

            //$data['item']=Query_helper::get_info($this->config->item('table_ams_requisition_request'),array('*'),array('id ='.$item_id, 'status!="'.$this->config->item('system_status_delete').'"'),1,0,array('id ASC'));
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
                System_helper::invalid_try('Receive Non Exists',$item_id);
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
            if($data['item']['status_receive']==$this->config->item('system_status_received'))
            {
                $ajax['status']=false;
                $ajax['system_message']='Purchase order already received.';
                $this->json_return($ajax);
            }
            if($data['item']['status_approve']!=$this->config->item('system_status_approved'))
            {
                $ajax['status']=false;
                $ajax['system_message']='Purchase order is not approved.';
                $this->json_return($ajax);
            }
            $data['categories']=$this->get_parent_wise_task();
            $data['info_basic']=Ams_helper::get_basic_info($data['item']);

            $data['title']="Purchase Order Receive";
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/receive",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/receive/'.$item_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_save_receive()
    {
        $id = $this->input->post("id");
        $user = User_helper::get_user();
        $time=time();
        $item_head=$this->input->post('item');
        $items=$this->input->post('items');
        if($id>0)
        {
            if(!((isset($this->permissions['action7']) && ($this->permissions['action7']==1))))
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
            }
            if($item_head['status_receive']!=$this->config->item('system_status_received'))
            {
                $ajax['status']=false;
                $ajax['system_message']='Receive Field is required.';
                $this->json_return($ajax);
            }
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
        $status_empty=false;
        if(sizeof($items)>0)
        {
            for($i=0; $i<sizeof($items); $i++)
            {
                if(!($items[$i]))
                {
                    $status_empty=true;
                    break;
                }
            }
        }
        if($status_empty)
        {
            $ajax['status']=false;
            $ajax['system_message']='Serial No/ID field is required.';
            $this->json_return($ajax);
        }
        $this->db->trans_start();  //DB Transaction Handle START

        $data=array();

        $data['date_received']=$time;
        $data['user_received']=$user->user_id;
        $data['date_warranty_start']=System_helper::get_time($item_head['date_warranty_start']);
        $data['date_warranty_end']=System_helper::get_time($item_head['date_warranty_end']);
        $data['status_receive']=$item_head['status_receive'];
        Query_helper::update($this->config->item('table_ams_requisition_request'),$data,array('id='.$id));

        for($i=0; $i<sizeof($items); $i++)
        {
            $data=array();
            $data['purchase_order_id']=$id;
            $data['serial_id']=$items[$i];
            $data['date_created']=$time;
            $data['user_created']=$user->user_id;
            Query_helper::add($this->config->item('table_ams_assets'),$data, false);
        }

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
