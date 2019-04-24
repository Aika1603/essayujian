<?php 

class Hasil_tes_model extends CI_model {

    // get dictionary
    function get_dictionary($id)
    {
        $query=$this->db->query("SELECT * from dictionary where word ='$jawab' LIMIT 1");
        return $query->result();
    }

 // get all
    function get_all()
    {
         $query=$this->db->query("SELECT siswa.*, nilai_akhir.`nilai_akhir` FROM siswa JOIN nilai_akhir ON siswa.`nis` = nilai_akhir.`nis` order by id_siswa ASC; ");
        return $query->result();
    }
      function get_all_detail($id)
    {
         $query=$this->db->query("SELECT soal_ujian.`pertanyaan`, hasil_akhir_ujian.* FROM soal_ujian JOIN hasil_akhir_ujian ON soal_ujian.`id_soal` = hasil_akhir_ujian.`id_soal` WHERE  nis=".$id."");
        return $query->result();
    }
	 // get data by id
    function get_by_id($id)
    {
        $this->db->where($this->id, $id);
        return $this->db->get($this->table)->row();
    }
    // delete data
    function delete($id)
    {
        $this->db->where('nis', $id);
        $this->db->delete('nilai_akhir');
    }
     // delete data
    function delete2($id)
    {
        $this->db->where('nis', $id);
        $this->db->delete('hasil_akhir_ujian');
    }
}