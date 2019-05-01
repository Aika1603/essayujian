<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login_model extends CI_model {

	public function getlogin($u,$p)
	{
		$pwd = md5($p);
		//cek admin
		$this->db->where(array('nama'=>$u, 'password' => $pwd));
		$query = $this->db->get('admin');	
		//cek siswa
		$this->db->where(array('nis'=>$u, 'password' => $pwd));
		$query2 = $this->db->get('siswa');	
		if($query->num_rows()>0)
		{   
			
				//mengambil data admin
				$row = $query->row();
				$sess  = array( 
							   'id_admin' 		=> $row->id_admin,
							   'username' 		=> $row->nama,
							   'password' 		=> $row->password,
							   'foto'			=> $row->foto);
				$this->session->set_userdata($sess);

				redirect('home/log_admin');
			
			
			
		}else if ($query2->num_rows()>0) {
			
				//mengambil data siswa
				$row = $query->row();
				$sess  = array( 
							   'id_siswa' 		=> $row->id_siswa,
							   'nis' 		=> $row->nis,
							   'username' 		=> $row->nama,
							   'password' 		=> $row->password,
							   'foto'			=> $row->foto);
				$this->session->set_userdata($sess);
				redirect('home/log_siswa');
			
		}
		else
		{
			$this->session->set_flashdata('info','maaf username atau password salah');
			redirect('login');
		}
	}
    
	
	
	

}

