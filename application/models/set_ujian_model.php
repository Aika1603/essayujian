
<?php 

class Set_ujian_model extends CI_model {

	public $table = 'set_ujian';
    public $id = 'id_set_ujian';
    public $order = 'ASC';
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

    // update data
    function update($id, $data)
    {
        $this->db->where($this->id, $id);
        $this->db->update($this->table, $data);
    }
    function get_total_soal(){
        $query=$this->db->query("select count(*) as total from soal_ujian");
        return $query;
    }

	

}

