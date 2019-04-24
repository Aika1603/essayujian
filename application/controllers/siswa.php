<?php

class Siswa extends CI_Controller {
 
 function __construct()
    {
        parent::__construct();
        $this->load->model('siswa_model');
        $this->load->library('form_validation');
        $this->load->library(array('upload'));
    }

public function index()
	{
		$this->security_model->getsecurity();
        $siswa = $this->siswa_model->get_all();

        $data = array( 
            'siswa_data' => $siswa
        );
		$data['sub_judul']='> Siswa';
		$data['content']='admin/vsiswa/siswa_list';
		$this->load->view('admin/index',$data);
	}

public function create(){
	    $this->security_model->getsecurity();
	    $data = array(
        'action' => site_url('siswa/create_action'),
	    'nis' => set_value('nis'),
        'nama' => set_value('nama'),
	    'foto' => set_value('foto'),
	    'password' => set_value('password'),
	);  
		$data['sub_judul']='Tambah Siswa';
		$data['content']='admin/vsiswa/siswa_add';
		$this->load->view('admin/index',$data);
	}

    public function create_action() {
      $this->security_model->getsecurity();
        $this->_rules();

        if ($this->form_validation->run() == FALSE) {
            $this->create();
        } else {
        $nmfile = "file_".time(); //nama file saya beri nama langsung dan diikuti fungsi time
        $config['upload_path'] = './assets/images'; //path folder
        $config['allowed_types'] = 'gif|jpg|png|jpeg|bmp'; //type yang dapat diakses bisa anda sesuaikan
        $config['max_size'] = '3072'; //maksimum besar file 3M
        $config['max_width']  = '5000'; //lebar maksimum 5000 px
        $config['max_height']  = '5000'; //tinggi maksimum 5000 px
        $config['file_name'] = $nmfile; //nama yang terupload nantinya

        $this->upload->initialize($config);
        
        if($_FILES['foto']['name'])
        {
            if ($this->upload->do_upload('foto'))
            {
                $gbr = $this->upload->data();
                $data = array( 
                    'nis' => $this->input->post('nis',TRUE),
                    'nama' => $this->input->post('nama',TRUE),
                    'foto' => $gbr['file_name'],
                    'password' => md5($this->input->post('password',TRUE))                            
                );

                $this->siswa_model->insert($data); //akses model untuk menyimpan ke database

                $config2['image_library'] = 'gd2'; 
                $config2['source_image'] = $this->upload->upload_path.$this->upload->file_name;
                $config2['new_image'] = './assets/images/thumb/'; // folder tempat menyimpan hasil resize
                $config2['maintain_ratio'] = false;
                $config['create_thumb']=true;
                $config2['quality'] = '100%';
                $config2['width'] =180; //lebar setelah resize menjadi 100 px
                $config2['height'] =200; //panjang setelah resize menjadi 100 px
                $this->load->library('image_lib',$config2); 


                $this->index();
                //pesan yang muncul jika resize error dimasukkan pada session flashdata
             if ( !$this->image_lib->resize()){
                $this->session->set_flashdata('errors', $this->image_lib->display_errors('', ''));   
              }
                //pesan yang muncul jika berhasil diupload pada session flashdata
                $this->session->set_flashdata("message", "Data Siswa Berhasil Disimpan");
                redirect(site_url('siswa')); //jika berhasil maka akan ditampilkan view upload
            }else{
                //pesan yang muncul jika terdapat error dimasukkan pada session flashdata
                $this->session->set_flashdata("message", 'Gagal Upload Gambar');
                redirect(site_url('siswa/create')); //jika gagal maka akan ditampilkan form upload
            }
        }else{
            $data = array(
                   
                    'nis' => $this->input->post('nis',TRUE),
                    'nama' => $this->input->post('nama',TRUE),
                    'password' => md5($this->input->post('password',TRUE)),
                );
                $this->siswa_model->insert($data); //akses model untuk menyimpan ke database
                //pesan yang muncul jika berhasil diupload pada session flashdata
                $this->session->set_flashdata("message", "Data Siswa Berhasil Disimpan");

                $this->index();
            }
        }                    
    } 

    public function update($id) 
    {    
        $this->security_model->getsecurity();
        $row = $this->siswa_model->get_by_id($id);

        if ($row) {
            $data = array(
                'action' => site_url('siswa/update_action'),
                'id_siswa' => set_value('id_siswa', $row->nis),
                'nis' => set_value('nis', $row->nis),
                'nama' => set_value('nama', $row->nama),
                'foto' => set_value('foto', $row->foto),
                'password' => set_value('password'),


        ); 
        
            $data['content']     = 'admin/vsiswa/siswa_update';
            $data['sub_judul']   = 'Edit Siswa';
            $this->load->view('admin/index', $data);
        } else {
            $this->session->set_flashdata('message', 'Baris Tidak Ditemukan');
            redirect(site_url('siswa'));
        }
    }
    
     public function update_action() 
    {    
        $this->security_model->getsecurity();
        $this->_rules2();
        
        if ($this->form_validation->run() == FALSE) {
            $this->update($this->input->post('id_siswa', TRUE));
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
        if($_FILES['foto']['name'])
        {
            if ($this->upload->do_upload('foto'))
            {
            
             $id =  $this->input->post('id_siswa', TRUE);
             $row = $this->siswa_model->get_by_id($id);

                    $path1 = './assets/images/'.$row->foto;
                    $path2 = './assets/images/thumb/'.$row->foto;
                    unlink($path1); 
                    unlink($path2); 
             //masukan baru
            $gbr = $this->upload->data();
            $data = array(
            'nis' => $this->input->post('nis',TRUE),
            'nama' => $this->input->post('nama',TRUE),
            'password' => md5($this->input->post('password',TRUE)),
            'foto' =>$gbr['file_name'],
            
            );
              
               $this->siswa_model->update($this->input->post('id_siswa', TRUE), $data);
                    
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

                $this->session->set_flashdata("message", "Data Siswa Berhasil Diedit");
                redirect(site_url('siswa')); 
            }else{
                //pesan yang muncul jika terdapat error upload gambar
                $this->session->set_flashdata("message", 'Gagal Upload Gambar');
                redirect(site_url('siswa/update')); //jika gagal maka akan ditampilkan form upload
            }
            

            }else{

            $data = array(
            'nis' => $this->input->post('nis',TRUE),
            'nama' => $this->input->post('nama',TRUE),
            'password' => md5($this->input->post('password',TRUE)),
            );
              
             $this->siswa_model->update($this->input->post('id_siswa', TRUE), $data);  
             $this->session->set_flashdata("message", "Data Siswa Berhasil Diedit");
                redirect(site_url('siswa')); 
            }

        }
    }
    
    
    public function delete($id) 
    {   
        $this->security_model->getsecurity();
        $row = $this->siswa_model->get_by_id($id);

        if ($row) {
            //hapus foto di folder
             $foto =  $row->foto;
            
            $path1 = './assets/images/'.$foto;
            $path2 = './assets/images/thumb/'.$foto;
            unlink($path1); 
            unlink($path2);
            //hapus foto di database
            $this->siswa_model->delete($id);
            $this->session->set_flashdata('message', 'Data Siswa Telah Dihapus'.$data);
            redirect(site_url('siswa'));
        } else {
            $this->session->set_flashdata('message', 'Data Siswa Tidak Ada');
            redirect(site_url('siswa'));
        }
    }

 public function _rules() {
	$this->form_validation->set_rules('nis', 'nis', 'trim|required');
	$this->form_validation->set_rules('nama', 'nama', 'trim|required');
	$this->form_validation->set_rules('password', 'password', 'trim|required');
	$this->form_validation->set_error_delimiters('<span class="text-danger">', '</span>');
    
    }

public function _rules2(){
 	$this->form_validation->set_rules('nis', 'nis', 'trim|required');
	$this->form_validation->set_rules('nama', 'nama', 'trim|required');
	$this->form_validation->set_error_delimiters('<span class="text-danger">', '</span>');
    
    }
}
