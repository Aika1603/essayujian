<?php 

class Ujian_siswa_model extends CI_model {

	function get_menit(){
        $query=$this->db->query("select waktu_ujian_menit as menit from set_ujian");
        return $query;
    }
    function get_total_menit(){
        $query=$this->db->query("select waktu_ujian_menit as total from set_ujian");
        return $query;
    }
   function get_total_soal(){
        $query=$this->db->query("select count(*) as total from soal_ujian");
        return $query;
    }

   function get_soal($no){
        $query=$this->db->query("SELECT * FROM soal_ujian ORDER BY id_soal ASC LIMIT ".$no.",1");
        return $query;
    }
	 // get data by id
    function get_by_id($id)
    {
        $query=$this->db->query("SELECT * FROM nilai_akhir where nis=".$id."");
        return $query;
    }

    //============================================================================================================
    function get_by_id_stemming($jawab)
    {
        $query=$this->db->query("SELECT * FROM dictionary where word ='$jawab' LIMIT 1");
        return $query;
    }

    //============================================================================================================
    function get_word()
    {
        $query=$this->db->query("SELECT * FROM dictionary");
        return $query;
    }
    //============================================================================================================
    function get_by_id_stopword($jawab)
    {
        $query=$this->db->query("SELECT * FROM dictionary where word ='$jawab' and stopword ='Bukan'");
        return $query;
    }
    //============================================================================================================

	 // insert data
    function insert_jawaban($data)
    {
        $this->db->insert('hasil_ujian', $data);
    }

    function insert_hasil($data,$id)
    {
        $this->db->where('nip', $id);
        $this->db->insert('ujian', $data);
    }

    function insert_detail($data)
    {
        $this->db->insert('detail_ujian', $data);
    }

    function Simpan($table, $data){
        return $this->db->insert($table, $data);
    }
}