<?php

class Hasil_tes extends CI_Controller {
 function __construct()
    {
        parent::__construct();
        $this->load->model('hasil_tes_model');
        $this->load->library('form_validation');
    }

public function index()
	{
		$this->security_model->getsecurity();
        $admin = $this->hasil_tes_model->get_all();
        $data = array(
            'admin_data' => $admin
        );
		$data['sub_judul']='> Hasil Tes';
		$data['content']='admin/vhasil/hasil_list';
		$this->load->view('admin/index',$data);
	}

public function lihat_detail($id){
    $detail = $this->hasil_tes_model->get_all_detail($id);
    $data = array(
            'detail_data' => $detail
    );
        $data['sub_judul']='> Hasil Tes > Detail';
        $data['content']='admin/vhasil/detail_list';
        $this->load->view('admin/index',$data);
}

public function lihat_detail2($id){
    $detail = $this->hasil_tes_model->get_all_detail($id);
    $data = array(
            'detail_data' => $detail
    );
        $data['sub_judul']='> Hasil Tes > Detail';
        $data['content']='admin/vhasil/detail_list2';
        $this->load->view('admin/index',$data);
}

public function lihat_detail3($id){
    $detail = $this->hasil_tes_model->get_all_detail($id);
    $data = array(
            'detail_data' => $detail
    );
        $data['sub_judul']='> Hasil Tes > Detail';
        $data['content']='admin/vhasil/detail_list3';
        $this->load->view('admin/index',$data);
}
    
public function delete($id) 
    {   
        $this->security_model->getsecurity();
        $this->hasil_tes_model->delete($id);
        $this->hasil_tes_model->delete2($id);
        $this->session->set_flashdata('message', 'Data Hasil Tes Telah Dihapus');
        redirect(site_url('hasil_tes'));
    }
}