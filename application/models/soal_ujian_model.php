
<?php 

class Soal_ujian_model extends CI_model {

	public $table = 'soal_ujian';
    public $id = 'id_soal';
    public $order = 'DESC';

	 // get all
    function get_all()
    {
        $this->db->order_by($this->id, $this->order);
        return $this->db->get($this->table)->result();
    }
    
	 // get data by id
    function get_by_id($id)
    {
        $this->db->where($this->id, $id);
        return $this->db->get($this->table)->row();
    }
     // insert data
    function insert($data)
    {
        $this->db->insert($this->table, $data);
    }

    // update data
    function update($data)
    {
        $this->db->where($this->id, $data['id_soal']);
        $this->db->update($this->table, $data);
    }

    // delete data
    function delete($id)
    {
        $this->db->where($this->id, $id);
        $this->db->delete($this->table);
    }
    // simpan data
    function Simpan($table, $data){
        return $this->db->insert($table, $data);
    }

    //cek kamus
    function cekKamusDasar($jawab)
    {
        $query=$this->db->query("SELECT basic_word FROM kata_dasar where basic_word ='$jawab'");
        return $query;
    }

}