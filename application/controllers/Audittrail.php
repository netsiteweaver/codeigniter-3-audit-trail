<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Audittrail extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->data['controller']   = str_replace("-","",$this->uri->segment(1,"dashboard"));
        $this->data['method']       = $this->uri->segment(2,"index");
        $this->load->model("logger_model");
        $this->mybreadcrumb->add('Audit Trail', base_url('audittrail/listing'));

        $this->load->model("accesscontrol_model");
        $this->data['perms']['view'] = $this->accesscontrol_model->authorised("audittrail","view");

        $this->load->model("audittrail_model");
    }

    public function listing()
    {
        //Access Control
        if(!isAuthorised(get_class(),"listing")) return false;

        $page = $this->uri->segment(3,1);
        $this->data['rpp'] = isset($_SESSION['rpp'])?$_SESSION['rpp']:'50';
        $this->data['rows'] = $this->audittrail_model->getPaged($page,$this->data['rpp']);
        $this->data['users'] = $this->audittrail_model->getUsers();
        $this->data['ips'] = $this->audittrail_model->getIPs();

        $this->data['filter_start_date'] = isset($_SESSION['filter_start_date'])?$_SESSION['filter_start_date']:"";
        $this->data['filter_end_date'] = isset($_SESSION['filter_end_date'])?$_SESSION['filter_end_date']:"";
        $this->data['filter_user'] = isset($_SESSION['filter_user'])?$_SESSION['filter_user']:"";
        $this->data['filter_ip'] = isset($_SESSION['filter_ip'])?$_SESSION['filter_ip']:"";

        $config['base_url'] = base_url("audittrail/listing/");
        $config['total_rows'] = $this->audittrail_model->totalRows();
        $config['per_page'] = $this->data['rpp'];
        $config['uri_segment'] = 3;
        $config['use_page_numbers'] = TRUE;
        $config['num_links'] = 10;
        $config['full_tag_open'] = "<ul class='pagination'>";
        $config['full_tag_close'] = "</ul>";
        $config['first_link'] = false;
        $config['last_link'] = false;
        $config['next_link'] = false;
        $config['prev_link'] = false;
        $config['num_tag_open'] = "<li>";
        $config['num_tag_close'] = "</li>";
        $config['cur_tag_open'] = "<li class='active'><a href='#'>";
        $config['cur_tag_close'] = "</a></li>";
        $this->load->library('pagination');
        $this->pagination->initialize($config);
        $this->data['pagination'] = $this->pagination->create_links();

        $this->mybreadcrumb->add('List', base_url('audittrail/listing'));
        $this->data['breadcrumbs'] = $this->mybreadcrumb->render();

        $this->data["content"]=$this->load->view("/audittrail/listing",$this->data,true);
        $this->load->view("/layouts/default",$this->data);      
    }

    public function view()
    {
        $this->mybreadcrumb->add('List', base_url('audittrail/listing'));
        $this->mybreadcrumb->add('View', base_url('audittrail/view'),"fa-eye");
        $this->data['breadcrumbs'] = $this->mybreadcrumb->render();

        $id = $this->uri->segment(3);
        $this->data['record'] = $this->audittrail_model->get($id);

        $this->data["content"]=$this->load->view("/audittrail/view",$this->data,true);
        $this->load->view("/layouts/default",$this->data);      
    }

    public function set_rpp()
    {
        $rpp = $this->uri->segment(3);
        $_SESSION['rpp'] = $rpp;
    }

    public function filters()
    {
        $_SESSION['filter_start_date'] = $this->input->post('start_date');
        $_SESSION['filter_end_date'] = $this->input->post('end_date');
        $_SESSION['filter_user'] = $this->input->post('user');
        $_SESSION['filter_ip'] = $this->input->post('ip');
        
    }

    public function clearFilters()
    {
        unset($_SESSION['filter_start_date']);
        unset($_SESSION['filter_end_date']);
        unset($_SESSION['filter_user']);
        unset($_SESSION['filter_ip']);
    }

}