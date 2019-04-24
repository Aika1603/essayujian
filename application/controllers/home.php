<?php
class Home extends CI_Controller {
	function __construct() {
        parent::__construct();
         $this->load->model('ujian_siswa_model');
    }
	function log_admin() {
			$this->security_model->getsecurity();
			$data['sub_judul']=' ';
			$data['content']='admin/welcome';
			$this->load->view('admin/index',$data);
	}
	function log_siswa() {
		$this->security_model->getsecurity();
		$id=$this->session->userdata('nis');

		$baris=$this->ujian_siswa_model->get_by_id($id);
		if  ($baris->num_rows()==null)
		{
			//random soal
			$ambil_data3 = $this->db->query("SELECT * FROM soal_ujian ;");
	        $total_data = $ambil_data3->num_rows();

			$nomorpertanyaan = array(); //untuk array nomor pertanyaan yang valid(tidak kembar)
	        $ceknomor = array(); //untuk array yang digunakan untuk melakukan pengecekan
	        $max = $total_data;
	        for ($i=0;$i<$max;$i++)
	        {
	            $nomor = rand(1,$max); //nomor hasil random antara 1-60
	            while(in_array($nomor,$ceknomor)) //fungsi in_array = cek apakah $nomor ada dalam array $ceknomor
	            {
	                $nomor = rand(1,$max); //diulang sampai tidak sama
	            }
	            $ceknomor[$i] = $nomor; //simpan nomor yang valid dalam array $ceknomor ke-i

	            $nomorpertanyaan[$i] = $nomor;
	            //echo $nomorpertanyaan[$i]." ";
	            $nomornya = $nomorpertanyaan[$i];
	            $masukkan_no_soal= $this->db->query("INSERT INTO random_soal (nis, no_soal) VALUES ('$id','$nomornya')");
	        }

			$query=$this->ujian_siswa_model->get_soal(0);
			//jika tidak ada soal ujian
			if ($query->num_rows()==null) {
				$data['content']='siswa/list_keterangan_kosong';
				$this->load->view('siswa/index',$data);

			}else{
			    $this->security_model->getsecurity();
			    $query=$this->ujian_siswa_model->get_total_menit();
		        foreach ($query->result() as $row){
		            $data['total_menit']= $row->total;
		        }

		       $query2=$this->ujian_siswa_model->get_total_soal();
		         foreach ($query2->result() as $row){
		            $data['total_soal']= $row->total;
		        }
			    $data['content']='siswa/list_keterangan';
				$this->load->view('siswa/index',$data);
			 }
		}else{
			$nama=$this->session->userdata('username');
			$query=$this->ujian_siswa_model->get_by_id($id);
			foreach ($query->result() as $row){
					            $nilai= $row->nilai_akhir;
					          //  $waktu= $row->waktu_ujian;
			}
			$data = array(
							'nis' => $id,
		                    'nama' => $nama,
		                    'nilai_ujian' => $nilai,
		                    //'waktu_ujian' => $waktu,
		                );
			$data['content']='siswa/list_hasil_ujian';
			$this->load->view('siswa/index',$data);
		}
	}
}