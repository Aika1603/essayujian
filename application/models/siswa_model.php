
<?php 

class Siswa_model extends CI_model {

	public $table = 'siswa';
    public $id = 'id_siswa';
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
     // insert data
    function insert($data)
    {
        $this->db->insert($this->table, $data);
    }

    // update data
    function update($id, $data)
    {
        $this->db->where("nis", $id);
        $this->db->update($this->table, $data);
    }
    
    // delete data
    function delete($id)
    {
        $this->db->where($this->id, $id);
        $this->db->delete($this->table);
    }
}