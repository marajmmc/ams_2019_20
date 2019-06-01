<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Purchase_requisition_payment extends Root_Controller
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
        $this->lang->language['LABEL_STATUS_PAYMENT_APPROVE']='Approve Status';

        $this->lang->language['LABEL_DATE_PAYMENT']='Payment Date';
        $this->lang->language['LABEL_IS_ADVANCE']='Is Advance';
        $this->lang->language['LABEL_AMOUNT']='Paid Amount';
        $this->lang->language['LABEL_REVISION_COUNT']='Revision Count (Edit Payment)';
    }
    public function index($action="list",$id=0,$id1=0)
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
        elseif($action=="list_payment")
        {
            $this->system_list_payment($id);
        }
        elseif($action=="get_items_payment")
        {
            $this->system_get_items_payment($id);
        }
        elseif($action=="add_payment")
        {
            $this->system_add_payment($id,$id1);
        }
        elseif($action=="edit_payment")
        {
            $this->system_edit_payment($id,$id1);
        }
        elseif($action=="save_payment")
        {
            $this->system_save_payment();
        }
        elseif($action=="payment_approve")
        {
            $this->system_payment_approve($id);
        }
        elseif($action=="save_payment_approve")
        {
            $this->system_save_payment_approve();
        }
        elseif($action=="details")
        {
            $this->system_details($id);
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
            $data['quantity_total']= 1;
            $data['amount_price_unit']= 1;
            $data['amount_price_total']= 1;
            $data['specification']= 1;
            $data['reason']= 1;
            $data['remarks']= 1;
        }
        else if($method=='list_all')
        {
            $data['id']= 1;
            //$data['date_requisition']= 1;
            $data['supplier_name']= 1;
            $data['category_name']= 1;
            $data['quantity_total']= 1;
            $data['amount_price_unit']= 1;
            $data['amount_price_total']= 1;
            $data['specification']= 1;
            $data['reason']= 1;
            $data['remarks']= 1;
            $data['status_payment_approve']= 1;
        }
        else if($method=='list_payment')
        {
            $data['id']= 1;
            $data['date_payment']= 1;
            $data['is_advance']= 1;
            $data['amount']= 1;
            $data['remarks']= 1;
            $data['revision_count']= 1;
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
            $data['title']="Purchase Order Payment Pending List";
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
        $this->db->where('item.status_payment_approve',$this->config->item('system_status_pending'));
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
            $data['title']="Purchase Order Payment All List";
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
    private function system_payment_approve($id)
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
                System_helper::invalid_try('Payment Approved Non Exists',$item_id);
                $ajax['status']=false;
                $ajax['system_message']='Invalid Requisition.';
                $this->json_return($ajax);
            }
            if($data['item']['status_payment_approve']==$this->config->item('system_status_approved'))
            {
                $ajax['status']=false;
                $ajax['system_message']='Purchase Order already payment approved.';
                $this->json_return($ajax);
            }
            if($data['item']['status_approve']!=$this->config->item('system_status_approved'))
            {
                $ajax['status']=false;
                $ajax['system_message']='Purchase Order already rejected.';
                $this->json_return($ajax);
            }

            $data['payments']=Query_helper::get_info($this->config->item('table_ams_requisition_payment'),array('*'),array('purchase_order_id ='.$item_id));

            //$data['categories']=$this->get_parent_wise_task();
            $data['info_basic']=Ams_helper::get_basic_info($data['item']);

            $data['title']="Purchase Order Payment Approved";
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/payment_approve",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/payment_approve/'.$item_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_save_payment_approve()
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
            if(!$item_head['status_payment_approve']==$this->config->item('system_status_approved'))
            {
                $ajax['status']=false;
                $ajax['system_message']='Approved field is required.';
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

        if($item_head['status_payment_approve']==$this->config->item('system_status_rollback'))
        {
            $data['remarks_payment']=$item_head['remarks_payment'];
            $data['status_approve']=$this->config->item('system_status_pending');
            $this->db->set('revision_count_payment_rollback', 'revision_count_payment_rollback+1', FALSE);
        }
        else
        {
            $data['date_payment_approved']=$time;
            $data['user_payment_approved']=$user->user_id;
            $data['remarks_payment']=$item_head['remarks_payment'];
            $data['status_payment_approve']=$item_head['status_payment_approve'];
            $this->db->set('revision_count_payment', 'revision_count_payment+1', FALSE);
        }
        Query_helper::update($this->config->item('table_ams_requisition_request'),$data,array('id='.$id));

        $this->db->trans_complete();  //DB Transaction Handle END

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
    private function system_list_payment($id)
    {
        if($id>0)
        {
            $data['item_id']=$id;
        }
        else
        {
            $data['item_id']=$this->input->post('id');
        }
        $user = User_helper::get_user();
        $method='list_payment';
        if(isset($this->permissions['action2'])&&($this->permissions['action2']==1))
        {
            $result=Query_helper::get_info($this->config->item('table_ams_requisition_request'),array('*'),array('id ='.$data['item_id'],'status!="'.$this->config->item('system_status_delete').'"'),1,0,array('id ASC'));
            if(!$result)
            {
                System_helper::invalid_try('Payment List Non Exists',$data['item_id']);
                $ajax['status']=false;
                $ajax['system_message']='Invalid Purchase Order.';
                $this->json_return($ajax);
            }
            if($result['status_payment_approve']==$this->config->item('system_status_approved'))
            {
                $ajax['status']=false;
                $ajax['system_message']='Purchase Order Payment Completed.';
                $this->json_return($ajax);
            }
            $data['info_basic']=Ams_helper::get_basic_info($result);
            $data['amount_total']=$result['amount_price_total'];

            $result=Query_helper::get_info($this->config->item('table_ams_requisition_payment'),array('SUM(amount) amount_total_paid'),array('purchase_order_id ='.$data['item_id'],'status!="'.$this->config->item('system_status_delete').'"'),1,0,array('id ASC'));
            $data['amount_total_paid']=$result['amount_total_paid']?$result['amount_total_paid']:0;
            $data['amount_total_due']=($data['amount_total']-$result['amount_total_paid']);

            $data['system_preference_items']= System_helper::get_preference($user->user_id, $this->controller_url, $method, $this->get_preference_headers($method));
            $data['title']="Purchase Order ID: (".$data['item_id'].") Payment List ";
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/list_payment",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/list_payment/'.$data['item_id']);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_get_items_payment($item_id)
    {
        $this->db->from($this->config->item('table_ams_requisition_payment').' item');
        $this->db->where('item.purchase_order_id',$item_id);
        $this->db->order_by('item.id','DESC');
        $items=$this->db->get()->result_array();
        foreach($items as &$item)
        {
            $item['date_payment']=System_helper::display_date($item['date_payment']);
        }
        $this->json_return($items);
    }
    private function system_add_payment($id)
    {
        if(isset($this->permissions['action1'])&&($this->permissions['action1']==1))
        {
            $data['title']="New Payment";
            $data['item']['id']=0;
            $data['item']['item_id']=$id;
            $data['item']['date_payment']=time();
            $data['item']['is_advance']=0;
            $data['item']['amount']='';
            $data['item']['remarks']='';

            $result=Query_helper::get_info($this->config->item('table_ams_requisition_request'),array('*'),array('id ='.$id,'status!="'.$this->config->item('system_status_delete').'"'),1,0,array('id ASC'));
            if(!$result)
            {
                System_helper::invalid_try('Payment List Non Exists',$id);
                $ajax['status']=false;
                $ajax['system_message']='Invalid Purchase Order.';
                $this->json_return($ajax);
            }
            if($result['status_payment_approve']==$this->config->item('system_status_approved'))
            {
                $ajax['status']=false;
                $ajax['system_message']='Purchase Order Payment Approved.';
                $this->json_return($ajax);
            }
            $data['info_basic']=Ams_helper::get_basic_info($result);
            $data['amount_total']=$result['amount_price_total'];
            //$data['model_number']=$result['model_number'];

            $result=Query_helper::get_info($this->config->item('table_ams_requisition_payment'),array('SUM(amount) amount_total_paid'),array('purchase_order_id ='.$id,'status!="'.$this->config->item('system_status_delete').'"'),1,0,array('id ASC'));
            $data['amount_total_paid']=$result['amount_total_paid']?$result['amount_total_paid']:0;
            $data['amount_total_due']=($data['amount_total']-$result['amount_total_paid']);

            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/add_edit_payment",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/add_payment/'.$id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_edit_payment($id, $id1)
    {
        if(isset($this->permissions['action2'])&&($this->permissions['action2']==1))
        {
            if($id1>0)
            {
                $data['item']['id']=$id1;
            }
            else
            {
                $data['item']['id']=$this->input->post('id');
            }
            $data['item']=Query_helper::get_info($this->config->item('table_ams_requisition_payment'),array('*'),array('id ='.$data['item']['id']),1,0,array('id ASC'));
            if(!$data['item'])
            {
                System_helper::invalid_try('Edit Non Exists',$data['item']['id']);
                $ajax['status']=false;
                $ajax['system_message']='Invalid Notice Type.';
                $this->json_return($ajax);
            }
            $data['item']['item_id']=$id;

            $result=Query_helper::get_info($this->config->item('table_ams_requisition_request'),array('*'),array('id ='.$data['item']['item_id'],'status!="'.$this->config->item('system_status_delete').'"'),1,0,array('id ASC'));
            $data['info_basic']=Ams_helper::get_basic_info($result);
            if(!$result)
            {
                System_helper::invalid_try('Payment List Non Exists',$data['item']['item_id']);
                $ajax['status']=false;
                $ajax['system_message']='Invalid Purchase Order.';
                $this->json_return($ajax);
            }
            if($result['status_payment_approve']==$this->config->item('system_status_approved'))
            {
                $ajax['status']=false;
                $ajax['system_message']='Purchase Order Payment  Approved.';
                $this->json_return($ajax);
            }
            $data['amount_total']=$result['amount_price_total'];
            //$data['model_number']=$result['model_number'];

            $result=Query_helper::get_info($this->config->item('table_ams_requisition_payment'),array('SUM(amount) amount_total_paid'),array('purchase_order_id ='.$data['item']['item_id'],'status!="'.$this->config->item('system_status_delete').'"'),1,0,array('id ASC'));
            $data['amount_total_paid']=$result['amount_total_paid']?$result['amount_total_paid']:0;
            $data['amount_total_due']=($data['amount_total']-$result['amount_total_paid']);

            $data['title']="Edit Payment";
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/add_edit_payment",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/edit_payment/'.$data['item']['item_id'].'/'.$data['item']['id']);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_save_payment()
    {
        $id = $this->input->post("id");
        $item_id = $this->input->post("item_id");
        $user = User_helper::get_user();
        $time=time();
        $item=$this->input->post('item');
        $result_amount=0;
        if($id>0)
        {
            if(!(isset($this->permissions['action2']) && ($this->permissions['action2']==1)))
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->json_return($ajax);
            }
            $result=Query_helper::get_info($this->config->item('table_ams_requisition_payment'),'*',array('id ='.$id),1);
            if(!$result)
            {
                System_helper::invalid_try('Update Non Exists',$id);
                $ajax['status']=false;
                $ajax['system_message']='Invalid Notice Type.';
                $this->json_return($ajax);
            }
            $result_amount=isset($result['amount'])?$result['amount']:0;
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
        if(!$this->check_validation_payment())
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->message;
            $this->json_return($ajax);
        }

        $result=Query_helper::get_info($this->config->item('table_ams_requisition_request'),array('*'),array('id ='.$item_id,'status!="'.$this->config->item('system_status_delete').'"'),1,0,array('id ASC'));
        if(!$result)
        {
            System_helper::invalid_try('Payment Update Non Exists',$id);
            $ajax['status']=false;
            $ajax['system_message']='Invalid Purchase Order.';
            $this->json_return($ajax);
        }
        if($result['status_payment_approve']==$this->config->item('system_status_approved'))
        {
            $ajax['status']=false;
            $ajax['system_message']='Purchase Order Payment Approved.';
            $this->json_return($ajax);
        }
        $amount_price_total=$result['amount_price_total'];

        $result=Query_helper::get_info($this->config->item('table_ams_requisition_payment'),array('SUM(amount) amount_total_paid'),array('purchase_order_id ='.$item_id,'status!="'.$this->config->item('system_status_delete').'"'),1,0,array('id ASC'));
        $amount_total_paid=$result['amount_total_paid']?$result['amount_total_paid']:0;

        if($item['amount'])
        {
            $amount_previous=$amount_total_paid-$result_amount;
            $current_paid_amount=$amount_previous+$item['amount'];
            if($amount_price_total<$current_paid_amount)
            {
                $ajax['status']=false;
                $ajax['system_message']='Purchase total amount is: '.$amount_price_total.' & your paid amount is: '.$current_paid_amount;
                $this->json_return($ajax);
            }
        }

        $this->db->trans_start();  //DB Transaction Handle START

        if($id>0)
        {
            //$data=array();
            $item['date_updated'] = $time;
            $item['user_updated'] = $user->user_id;
            $item['date_payment'] = System_helper::get_time($item['date_payment']);
            $this->db->set('revision_count', 'revision_count+1', FALSE);
            Query_helper::update($this->config->item('table_ams_requisition_payment'),$item, array('id='.$id), false);
        }
        else
        {
            $item['purchase_order_id'] = $item_id;
            $item['date_payment'] = System_helper::get_time($item['date_payment']);
            $item['date_created']=$time;
            $item['user_created']=$user->user_id;
            $item['revision_count']=1;
            Query_helper::add($this->config->item('table_ams_requisition_payment'),$item, false);
        }

        $this->db->trans_complete();   //DB Transaction Handle END
        if ($this->db->trans_status() === TRUE)
        {
            $save_and_new=$this->input->post('system_save_new_status');
            $this->message=$this->lang->line("MSG_SAVED_SUCCESS");
            if($save_and_new==1)
            {
                $this->system_add_payment($item_id);
            }
            else
            {
                $this->system_list_payment($item_id);
            }
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("MSG_SAVED_FAIL");
            $this->json_return($ajax);
        }
    }
    private function check_validation_payment()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('item[amount]',$this->lang->line('LABEL_AMOUNT'),'required');
        if($this->form_validation->run() == FALSE)
        {
            $this->message=validation_errors();
            return false;
        }
        return true;
    }
    private function system_details($id)
    {
        if(isset($this->permissions['action0'])&&($this->permissions['action0']==1))
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
                System_helper::invalid_try(__FUNCTION__,$item_id,'Forward Non Exists');
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try.';
                $this->json_return($ajax);
            }

            $data['categories']=Ams_helper::get_categories();
            $data['info_basic']=Ams_helper::get_basic_info($data['item']);

            $this->db->from($this->config->item('table_ams_requisition_file').' item');
            $this->db->where('item.status =',$this->config->item('system_status_active'));
            $this->db->order_by('item.ordering','ASC');
            $this->db->order_by('item.id','ASC');
            $this->db->where('item.purpose',$this->config->item('system_purpose_requisition_request'));
            $this->db->where('item.purchase_order_id',$item_id);
            $data['files']=$this->db->get()->result_array();

            $data['title']="Purchase Order Details :: ". $data['item']['id'];
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->common_view_location."/details",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/details/'.$item_id);
            $this->json_return($ajax);

        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }

}
