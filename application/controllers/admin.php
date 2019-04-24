<?php
class Admin extends CI_Controller {
 function __construct() {
        parent::__construct();
        $this->load->model('admin_model');
        $this->load->library('form_validation');
        $this->load->library(array('upload'));
    }
 public function index() {
		$this->security_model->getsecurity();
        $admin = $this->admin_model->get_all();
        $data = array(
            'admin_data' => $admin);
		$data['sub_judul']='> Admin';
		$data['content']='admin/vadmin/admin_list';
		$this->load->view('admin/index',$data);
	}   
 public function create() {
		$this->security_model->getsecurity();
		 $data = array(
        'action' => site_url('admin/create_action'),
	    'id_admin' => set_value('id_admin'),
	    'name' => set_value('username'),
        'pwd' => set_value('password'),
	    'email' => set_value('email'),
	    'foto' => set_value('foto'),);  
		$data['sub_judul']='> Tambah Admin';
		$data['content']='admin/vadmin/admin_add';
		$this->load->view('admin/index',$data);
	}
 public function create_action() {
      $this->security_model->getsecurity();
        if ($this->form_validation->run() == FALSE) {
            $this->create();
        }else{
        $nmfile = "file_".time(); //nama file saya beri nama langsung dan diikuti fungsi time
        $config['upload_path'] = './assets/images'; //path folder
        $config['allowed_types'] = 'gif|jpg|png|jpeg|bmp'; //type yang dapat diakses bisa anda sesuaikan
        $config['max_size'] = '3072'; //maksimum besar file 3M
        $config['max_width']  = '5000'; //lebar maksimum 5000 px
        $config['max_height']  = '5000'; //tinggi maksimum 5000 px
        $config['file_name'] = $nmfile; //nama yang terupload nantinya
        $this->upload->initialize($config);
        
        if($_FILES['foto']['name']) {
            if ($this->upload->do_upload('foto')) {
                $gbr = $this->upload->data();
                $data = array(
                    'id_admin' => $this->input->post('id_admin',TRUE),
                    'nama' => $this->input->post('name',TRUE),
                    'password' => md5($this->input->post('pwd',TRUE)),
                    'email' => $this->input->post('email',TRUE),
                    'foto' =>$gbr['file_name'],
                    );
                $this->admin_model->insert($data); //akses model untuk menyimpan ke database
                $config2['image_library'] = 'gd2'; 
                $config2['source_image'] = $this->upload->upload_path.$this->upload->file_name;
                $config2['new_image'] = './assets/images/thumb/'; // folder tempat menyimpan hasil resize
                $config2['maintain_ratio'] = false;
                $config['create_thumb']=true;
                $config2['quality'] = '100%';
                $config2['width'] =180; //lebar setelah resize menjadi 100 px
                $config2['height'] =200; //panjang setelah resize menjadi 100 px
                $this->load->library('image_lib',$config2); 
                //pesan yang muncul jika resize error dimasukkan pada session flashdata
                if ( !$this->image_lib->resize()){
                $this->session->set_flashdata('errors', $this->image_lib->display_errors('', ''));   
                }
                //pesan yang muncul jika berhasil diupload pada session flashdata
                $this->session->set_flashdata("message", "Admin berhasil di simpan");
                redirect(site_url('admin')); //jika berhasil maka akan ditampilkan view upload
                }else{
                //pesan yang muncul jika terdapat error dimasukkan pada session flashdata
                $this->session->set_flashdata("message", 'Gagal upload gambar');
                redirect(site_url('admin/create')); //jika gagal maka akan ditampilkan form upload
                }
            }else{
            $data = array(
                    'id_admin' => $this->input->post('id_admin',TRUE),
                    'nama' => $this->input->post('name',TRUE),
                    'email' => $this->input->post('email',TRUE),
                    'password' => md5($this->input->post('pwd',TRUE)), 
                );
                $this->admin_model->insert($data); //akses model untuk menyimpan ke database
                $this->session->set_flashdata("message", "Admin berhasil di simpan");
                redirect(site_url('admin')); //jika berhasil maka akan ditampilkan view upload
            }
        }                       
    } 
    public function update($id) {    
        $this->security_model->getsecurity();
        $row = $this->admin_model->get_by_id($id);
        if ($row) {
            $data = array(
        'action' => site_url('admin/update_action'),
        'id_admin' => set_value('id_admin', $row->id_admin),
        'name' => set_value('nama', $row->nama),
        'pwd' => set_value('password'),
        'email' => set_value('email', $row->email),
        'foto' => set_value('foto', $row->foto),
        ); 
            $data['content']     = 'admin/vadmin/admin_add';
            $data['sub_judul']   = '> Edit Admin';
            $this->load->view('admin/index', $data);
        } else {
            $this->session->set_flashdata('message', 'Baris Tidak Ditemukan');
            redirect(site_url('admin'));
        }
    }  
    public function update_action() {    
        $this->security_model->getsecurity();
        $this->_rules2();      
        if ($this->form_validation->run() == FALSE) {
            $this->update($this->input->post('id_admin', TRUE));
        } else {
        //Masukan gambar foto baru
        $nmfile = "file_".time(); //nama file saya beri nama langsung dan diikuti fungsi time
        $config['upload_path'] = './assets/images'; //path folder
        $config['allowed_types'] = 'gif|jpg|png|jpeg|bmp'; //type yang dapat diakses bisa anda sesuaikan
        $config['max_size'] = '3072'; //maksimum besar file 3M
        $config['max_width']  = '5000'; //lebar maksimum 5000 px
        $config['max_height']  = '5000'; //tinggi maksimum 5000 px
        $config['file_name'] = $nmfile; //nama yang terupload nantinya
        $this->upload->initialize($config);
        if($_FILES['foto']['name']) {
            if ($this->upload->do_upload('foto')) {
             $id =  $this->input->post('id_admin', TRUE);
             $row = $this->admin_model->get_by_id($id);
                    $path1 = './assets/images/'.$row->foto;
                    $path2 = './assets/images/thumb/'.$row->foto;
                    unlink($path1); 
                    unlink($path2); 
             //masukan baru
            $gbr = $this->upload->data();
            $data = array(
            'nama' => $this->input->post('name',TRUE),
            'email' => $this->input->post('email',TRUE),
            'password' => md5($this->input->post('pwd',TRUE)),
            'foto' =>$gbr['file_name'],
            );
               $this->admin_model->update($this->input->post('id_admin', TRUE), $data);
               //buat resize gambar baru
                $config2['image_library'] = 'gd2'; 
                $config2['source_image'] = $this->upload->upload_path.$this->upload->file_name;
                $config2['new_image'] = './assets/images/thumb/'; // folder tempat menyimpan hasil resize
                $config2['maintain_ratio'] = false;
                $config['create_thumb']=true;
                $config2['quality'] = '100%';
                $config2['width'] =180; //lebar setelah resize menjadi 100 px
                $config2['height'] =200; //panjang setelah resize menjadi 100 px
                $this->load->library('image_lib',$config2); 
                //hapus foto
                //pesan yang muncul jika resize error 
                  if ( !$this->image_lib->resize()){
                $this->session->set_flashdata('errors', $this->image_lib->display_errors('', ''));   
                } 
                $this->session->set_flashdata("message", "Data Admin berhasil di Edit");
                redirect(site_url('admin')); 
                }else{
                //pesan yang muncul jika terdapat error upload gambar
                $this->session->set_flashdata("message", 'Gagal upload gambar');
                redirect(site_url('admin/update')); //jika gagal maka akan ditampilkan form upload
                }
            }else{
            $data = array(
            'nama' => $this->input->post('name',TRUE),
            'email' => $this->input->post('email',TRUE),
            'password' => md5($this->input->post('pwd',TRUE)),
            );
               $this->admin_model->update($this->input->post('id_admin', TRUE), $data);  
                 $this->session->set_flashdata("Message", "Data Admin berhasil di Edit");
                redirect(site_url('admin')); 
            }
        }
    } 
    public function delete($id) {   
        $this->security_model->getsecurity();
        $row = $this->admin_model->get_by_id($id);
        if ($row) {
            //hapus foto di folder
            $foto =  $row->foto;
            $path1 = './assets/images/'.$foto;
            $path2 = './assets/images/thumb/'.$foto;
            unlink($path1); 
            unlink($path2);
            //hapus foto di database
            $this->admin_model->delete($id);
            $this->session->set_flashdata('message', 'Data Admin Telah Dihapus'.$data);
            redirect(site_url('admin'));
            }else{
            $this->session->set_flashdata('message', 'Data Admin Tidak Ada');
            redirect(site_url('admin'));
        }
    }
    public function _rules() {
	$this->form_validation->set_rules('name', 'nama', 'trim|required');
	$this->form_validation->set_rules('pwd', 'password', 'trim|required');
	$this->form_validation->set_rules('email', 'email', 'trim|required');
    //$this->form_validation->set_rules('foto', 'foto', 'trim|required');
	$this->form_validation->set_rules('id_admin', 'id_admin', 'trim');
	$this->form_validation->set_error_delimiters('<span class="text-danger">', '</span>');
    }
    public function _rules2() {
    $this->form_validation->set_rules('name', 'nama', 'trim|required');
    //$this->form_validation->set_rules('pwd', 'password', 'trim|required');
    $this->form_validation->set_rules('email', 'email', 'trim|required');
    //$this->form_validation->set_rules('foto', 'foto', 'trim|required');
    $this->form_validation->set_rules('id_admin', 'id_admin', 'trim');
    $this->form_validation->set_error_delimiters('<span class="text-danger">', '</span>');
    }
}
