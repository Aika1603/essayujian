<?php

class Set_ujian extends CI_Controller {
 
 function __construct()
    {
        parent::__construct();
        $this->load->model('set_ujian_model');
        $this->load->library('form_validation');
    }

public function index()
	{
		$this->security_model->getsecurity();
        $set_ujian = $this->set_ujian_model->get_all();         
        $data = array(
            'set_ujian_data' => $set_ujian
        );
         $query2=$this->set_ujian_model->get_total_soal();
             foreach ($query2->result() as $row){
              $data['total_soal']= $row->total;
            }
		$data['sub_judul']='> Set Ujian';
		$data['content']='admin/vset_ujian/set_ujian_list';
		$this->load->view('admin/index',$data);
	}
    
     public function update_action() 
    {    
        $this->security_model->getsecurity();
     
            $data = array(
            'waktu_ujian_menit' => $this->input->post('waktu',true), 
            );  
         $this->set_ujian_model->update('1', $data);
         redirect(site_url('set_ujian'));         
    }
}
