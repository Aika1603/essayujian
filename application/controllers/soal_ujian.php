<?php

class Soal_ujian extends CI_Controller {
 
 function __construct()
    {
        parent::__construct();
        $this->load->model('soal_ujian_model');
        $this->load->library('form_validation');
    }

public function index()
	{
		$this->security_model->getsecurity();
        $soal = $this->soal_ujian_model->get_all();

        $data = array(
            'soal_ujian_data' => $soal
        );
		$data['sub_judul']='> Soal Ujian';
		$data['content']='admin/vsoal_ujian/soal_ujian_list';
		$this->load->view('admin/index',$data);
	}
    
public function create(){
		$this->security_model->getsecurity();
		/*$data = array(
        'action' => site_url('soal_ujian/create_action'),
	    'id_soal' => set_value('id_soal'),
	    'pertanyaan' => set_value('pertanyaan'),
        'jawaban' => set_value('kunci_jawaban'),
	    ); */
		$data['sub_judul']='> Tambah Soal';
		$data['content']='admin/vsoal_ujian/soal_ujian_add';
		$this->load->view('admin/index',$data);
	}

 public function create_action() 
    {
      $this->security_model->getsecurity();
        $this->_rules();
        if ($this->form_validation->run() == FALSE) {
            $this->create();
        } else {
                $data = array(
                    'id_soal' => $this->input->post('id_soal',TRUE),
                    'pertanyaan' => $this->input->post('pertanyaan',TRUE),
                    'kunci_jawaban' => $this->input->post('jawaban',TRUE),    
                );
                $this->soal_ujian_model->insert($data); 
                $this->session->set_flashdata("message", "Data Soal Ujian Berhasil di Simpan");
                redirect(site_url('soal_ujian')); 
        }                                     
    } 

    public function update($id) 
    {    
        $this->security_model->getsecurity();
        $row = $this->soal_ujian_model->get_by_id($id);

        if ($row) {
            $data = array(
                'action' => site_url('soal_ujian/update_action'),
                'id_soal' => set_value('id_soal', $row->id_soal),
                'pertanyaan' => set_value('pertanyaan', $row->pertanyaan),
                'jawaban' => set_value('kunci_jawaban', $row->kunci_jawaban),
        ); 
            $data['content']     = 'admin/vsoal_ujian/soal_ujian_edit';
            $data['sub_judul']   = '> Edit Soal';
            $this->load->view('admin/index', $data);
        } else {
            $this->session->set_flashdata('message', 'Baris Tidak Ditemukan');
            redirect(site_url('soal_ujian'));
        }
    }
    
     public function update_action() 
    {    
        $this->security_model->getsecurity();
        $this->_rules();
        
        if ($this->form_validation->run() == FALSE) {
            $this->update($this->input->post('id_soal', TRUE));
        } else {
       
            $data = array(
            'id_soal' => $this->input->post('id_soal',TRUE),
            'pertanyaan' => $this->input->post('pertanyaan',TRUE),
            'kunci_jawaban' => $this->input->post('jawaban',TRUE), 
            );  
         $this->soal_ujian_model->update($data);
         $this->session->set_flashdata("Message", "Data Soal berhasil Di Edit");
        redirect(site_url('soal_ujian'));         
        }
    }
    
    public function delete($id) 
    {   
        $this->security_model->getsecurity();
        $row = $this->soal_ujian_model->get_by_id($id);

        if ($row) {
            //hapus foto di folder
             $foto =  $row->foto;
            
            $path1 = './assets/images/'.$foto;
            $path2 = './assets/images/thumb/'.$foto;
            unlink($path1); 
            unlink($path2);
            //hapus foto di database
            $this->soal_ujian_model->delete($id);
            $this->session->set_flashdata('message', 'Data Soal Telah Dihapus'.$data);
            redirect(site_url('soal_ujian'));
        } else {
            $this->session->set_flashdata('message', 'Data Soal Tidak Ada');
            redirect(site_url('soal_ujian'));
        }
    }

    public function _rules() {
	$this->form_validation->set_rules('pertanyaan', 'pertanyaan', 'trim|required');
	$this->form_validation->set_rules('jawaban', 'jawaban', 'trim|required');
	$this->form_validation->set_rules('id_soal', 'id_soal', 'trim');
	$this->form_validation->set_error_delimiters('<span class="text-danger">', '</span>');
    }

     // Rumus Stemmming-nya //
    function cekKamus($jawab){ 
        $a = $jawab;
        $query = $this->soal_ujian_model->cekKamusDasar($jawab);
        if(sizeof($query->result())==1){
            return true; // True jika ada
        }else{
            return false; // jika tidak ada FALSE
        }
    }
    //==================================================================================================

    //fungsi untuk menghapus suffix seperti -ku, -mu, -kah, dsb
    function Del_Inflection_Suffixes($jawab){ 
        $kataAsal = $jawab;
        
        if(preg_match('/([km]u|nya|[kl]ah|pun)\z/i',$jawab)){ // Cek Inflection Suffixes
            $__jawab = preg_replace('/([km]u|nya|[kl]ah|pun)\z/i','',$jawab);

            return $__jawab;
        }
        return $kataAsal;
    }

    //Cek Prefix Disallowed Sufixes (Kombinasi Awalan dan Akhiran yang tidak diizinkan)
    function Cek_Prefix_Disallowed_Sufixes($jawab){

        if(preg_match('/^(be)[[:alpha:]]+/(i)\z/i',$jawab)){ // be- dan -i
            return true;
        }
        if(preg_match('/^(se)[[:alpha:]]+/(i|kan)\z/i',$jawab)){ // se- dan -i,-kan
            return true;
        }
        if(preg_match('/^(di)[[:alpha:]]+/(an)\z/i',$jawab)){ // di- dan -an
            return true;
        }
        if(preg_match('/^(me)[[:alpha:]]+/(an)\z/i',$jawab)){ // me- dan -an
            return true;
        }
        if(preg_match('/^(ke)[[:alpha:]]+/(i|kan)\z/i',$jawab)){ // ke- dan -i,-kan
            return true;
        }
        return false;
    }

    //Hapus Derivation Suffixes ("-i", "-an" atau "-kan")
    function Del_Derivation_Suffixes($jawab){
        $kataAsal = $jawab;
        if(preg_match('/(i|an)\z/i',$jawab)){ // Cek Suffixes
            $__jawab = preg_replace('/(i|an)\z/i','',$jawab);
            if($this->cekKamus($__jawab)){ // Cek Kamus
                return $__jawab;
            }else if(preg_match('/(kan)\z/i',$jawab)){
                $__jawab = preg_replace('/(kan)\z/i','',$jawab);
                if($this->cekKamus($__jawab)){
                    return $__jawab;
                }
            }
    /*–Jika Tidak ditemukan di kamus–*/
        }
        return $kataAsal;
    }

    //Hapus Derivation Prefix ("di-", "ke-", "se-", "te-", "be-", "me-", atau "pe-")
    function Del_Derivation_Prefix($jawab){
        $kataAsal = $jawab;

        /* —— Tentukan Tipe Awalan ————*/
        if(preg_match('/^(di|[ks]e)/',$jawab)){ // Jika di-,ke-,se-
            $__jawab = preg_replace('/^(di|[ks]e)/','',$jawab);
            
            if($this->cekKamus($__jawab)){
                return $__jawab;
            }
            $__jawab__ = $this->Del_Derivation_Suffixes($__jawab);
                
            if($this->cekKamus($__jawab__)){
                return $__jawab__;
            }       
            if(preg_match('/^(diper)/',$jawab)){ //diper-
                $__jawab = preg_replace('/^(diper)/','',$jawab);
                $__jawab__ = $this->Del_Derivation_Suffixes($__jawab);
            
                if($this->cekKamus($__jawab__)){
                    return $__jawab__;
                }
            }
            if(preg_match('/^(ke[bt]er)/',$jawab)){  //keber- dan keter-
                $__jawab = preg_replace('/^(ke[bt]er)/','',$jawab);
                $__jawab__ = $this->Del_Derivation_Suffixes($__jawab);
            
                if($this->cekKamus($__jawab__)){
                    return $__jawab__;
                }
            }       
        }
        
        if(preg_match('/^([bt]e)/',$jawab)){ //Jika awalannya adalah "te-","ter-", "be-","ber-"
            
            $__jawab = preg_replace('/^([bt]e)/','',$jawab);
            if($this->cekKamus($__jawab)){
                return $__jawab; // Jika ada balik
            }           
            $__jawab = preg_replace('/^([bt]e[lr])/','',$jawab);    
            if($this->cekKamus($__jawab)){
                return $__jawab; // Jika ada balik
            }               
            $__jawab__ = $this->Del_Derivation_Suffixes($__jawab);
            if($this->cekKamus($__jawab__)){
                return $__jawab__;
            }
        }
        
        if(preg_match('/^([mp]e)/',$jawab)){
            $__jawab = preg_replace('/^([mp]e)/','',$jawab);
            if($this->cekKamus($__jawab)){
                return $__jawab; // Jika ada balik
            }
            $__jawab__ = $this->Del_Derivation_Suffixes($__jawab);
            if($this->cekKamus($__jawab__)){
                return $__jawab__;
            }           
            if(preg_match('/^(memper)/',$jawab)){
                $__jawab = preg_replace('/^(memper)/','',$jawab);
                if($this->cekKamus($jawab)){
                    return $__jawab;
                }
                $__jawab__ = $this->Del_Derivation_Suffixes($__jawab);
                if($this->cekKamus($__jawab__)){
                    return $__jawab__;
                }
            }
            
            if(preg_match('/^([mp]eng)/',$jawab)){
                $__jawab = preg_replace('/^([mp]eng)/','',$jawab);
                if($this->cekKamus($__jawab)){
                    return $__jawab; // Jika ada balik
                }
                $__jawab__ = $this->Del_Derivation_Suffixes($__jawab);
                if($this->cekKamus($__jawab__)){
                    return $__jawab__;
                }               
                $__jawab = preg_replace('/^([mp]eng)/','k',$jawab);
                if($this->cekKamus($__jawab)){
                    return $__jawab; // Jika ada balik
                }
                $__jawab__ = $this->Del_Derivation_Suffixes($__jawab);
                if($this->cekKamus($__jawab__)){
                    return $__jawab__;
                }
            }
            
            if(preg_match('/^([mp]eny)/',$jawab)){
                $__jawab = preg_replace('/^([mp]eny)/','s',$jawab);
                if($this->cekKamus($__jawab)){
                    return $__jawab; // Jika ada balik
                }
                $__jawab__ = $this->Del_Derivation_Suffixes($__jawab);
                if($this->cekKamus($__jawab__)){
                    return $__jawab__;
                }
            }
            
            if(preg_match('/^([mp]e[lr])/',$jawab)){
                $__jawab = preg_replace('/^([mp]e[lr])/','',$jawab);
                if($this->cekKamus($__jawab)){
                    return $__jawab; // Jika ada balik
                }
                $__jawab__ = $this->Del_Derivation_Suffixes($__jawab);
                if($this->cekKamus($__jawab__)){
                    return $__jawab__;
                }
            }
            
            if(preg_match('/^([mp]en)/',$jawab)){
                $__jawab = preg_replace('/^([mp]en)/','t',$jawab);
                if($this->cekKamus($__jawab)){
                    return $__jawab; // Jika ada balik
                }
                $__jawab__ = $this->Del_Derivation_Suffixes($__jawab);
                if($this->cekKamus($__jawab__)){
                    return $__jawab__;
                }               
                $__jawab = preg_replace('/^([mp]en)/','',$jawab);
                if($this->cekKamus($__jawab)){
                    return $__jawab; // Jika ada balik
                }
                $__jawab__ = $this->Del_Derivation_Suffixes($__jawab);
                if($this->cekKamus($__jawab__)){
                    return $__jawab__;
                }
            }
                
            if(preg_match('/^([mp]em)/',$jawab)){
                $__jawab = preg_replace('/^([mp]em)/','',$jawab);
                if($this->cekKamus($__jawab)){
                    return $__jawab; // Jika ada balik
                }
                $__jawab__ = $this->Del_Derivation_Suffixes($__jawab);
                if($this->cekKamus($__jawab__)){
                    return $__jawab__;
                }               
                $__jawab = preg_replace('/^([mp]em)/','p',$jawab);
                if($this->cekKamus($__jawab)){
                    return $__jawab; // Jika ada balik
                }
                
                $__jawab__ = $this->Del_Derivation_Suffixes($__jawab);
                if($this->cekKamus($__jawab__)){
                    return $__jawab__;
                }
            }   
        }
        return $kataAsal;
    }

    //fungsi pencarian akar kata
    function stemming($jawab){ 
        $kataAsal = $jawab;
        $cekKata = $this->cekKamus($jawab);
        if($cekKata == true){ // Cek Kamus
            return $jawab; // Jika Ada maka kata tersebut adalah kata dasar
        }else{ //jika tidak ada dalam kamus maka dilakukan stemming
            $jawab = $this->Del_Inflection_Suffixes($jawab);
            if($this->cekKamus($jawab)){
                return $jawab;
            }           
            $jawab = $this->Del_Derivation_Suffixes($jawab);
            if($this->cekKamus($jawab)){
                return $jawab;
            }           
            $jawab = $this->Del_Derivation_Prefix($jawab);
            if($this->cekKamus($jawab)){
                return $jawab;
            }
            if($this->cekKamus($jawab) == false){
                return $jawab;
            }else{
                return $kataAsal;
            }
        }
    }

    public function proses() 
    {    
        $this->db->query('TRUNCATE token;');
        $tanggal_terakhir = $this->db->query('SELECT * FROM soal_ujian;');
        foreach ($tanggal_terakhir->result_array() as $row) {
            $id_soal        = $row['id_soal'];
            $kunci_jawaban  = $row['kunci_jawaban'];
            // CASE FOLDING dan PROSES PENGHILANGAN SIMBOL//
            $case_folding = strtolower($kunci_jawaban);
            $case_folding= str_replace("'", " ", $case_folding);     
            $case_folding= str_replace("-", " ", $case_folding);     
            $case_folding= str_replace(")", " ", $case_folding);     
            $case_folding= str_replace("(", " ", $case_folding);            
            $case_folding= str_replace("\"", " ", $case_folding);    
            $case_folding= str_replace("/", " ", $case_folding);     
            $case_folding= str_replace("=", " ", $case_folding);     
            $case_folding= str_replace(".", " ", $case_folding);     
            $case_folding= str_replace(",", " ", $case_folding);     
            $case_folding= str_replace(":", " ", $case_folding);     
            $case_folding= str_replace(";", " ", $case_folding);     
            $case_folding= str_replace("!", " ", $case_folding);    
            $case_folding= str_replace("?", " ", $case_folding);    
            $case_folding= str_replace("`", " ", $case_folding);
            $case_folding= str_replace("~", " ", $case_folding);
            $case_folding= str_replace("@", " ", $case_folding);
            $case_folding= str_replace("#", " ", $case_folding);
            $case_folding= str_replace("$", " ", $case_folding);
            $case_folding= str_replace("%", " ", $case_folding);
            $case_folding= str_replace("^", " ", $case_folding);
            $case_folding= str_replace("&", " ", $case_folding);
            $case_folding= str_replace("*", " ", $case_folding);
            $case_folding= str_replace("_", " ", $case_folding);
            $case_folding= str_replace("+", " ", $case_folding);
            $case_folding= str_replace("[", " ", $case_folding);
            $case_folding= str_replace("]", " ", $case_folding);
            $case_folding= str_replace("<", " ", $case_folding);
            $case_folding= str_replace(">", " ", $case_folding);
            // TOKENIZING  dan FILTERING//
            $token = str_word_count(strtolower($case_folding), 1);
            foreach ($token as $key=>$hasil_token)
            {
                $otext=$hasil_token;
                $stopwords = array("a", "ada", "adalah", "adanya", "adapun", "an", "agak", "agaknya", "kordinasi", "agar", "
            akan", "akankah", "akhir", "asa", "akhirnya", "akibat", "akibatnya", "aku", "akulah", "amat", "amatlah", "
            anda", "andai", "andaikan", "andaikata", "andalah", "antar", "antara", "antaranya", "apa", "apaan", "apabila", "
            apakah", "apalagi", "apapun", "apatah", "asal", "asalkan", "atas", "atau", "ataukah", "ataupun", "b", "bagai", "
            bagaikan", "bagaimana", "bangsa", "bagaimanakah", "bagaimanapun", "bagi", "bahkan", "bahwa", "bahwasanya", "baik", "
            baiklah", "balik", "banyak", "barangkali", "baru", "bawah", "bebas", "beberapa", "begini", "beginian", "
            beginikah", "beginilah", "begitu", "begitukah", "begitulah", "begitupun", "belakang", "belum", "belumlah", "
            benar", "benar-benar", "berapa", "berapakah", "berapalah", "berapapun", "berbagai", "berbeda", "berhubung", "
            berikut", "berikutnya", "berkali-kali", "berkat", "bermacam", "bermacam-macam", "bersama", "bersama-sama", "besar", "
            beserta", "betul", "betulkah", "biar", "biarpun", "biasa", "biasanya", "bila", "bilakah", "bilamana", "bisa", "bisakah", "
            boleh", "bolehkah", "bolehlah", "buat", "bukan", "bukankah", "bukanlah", "bukannya", "c", "cenderung", "cermat", "contoh", "
            cukup", "cuma", "d", "dahulu", "dalam", "dan", "dapat", "dari", "darimana", "daripada", "dekat", "deng", "demi", "demikian", "
            demikianlah", "dengan", "depan", "di", "dia", "dialah", "diantara", "diantaranya", "dikarenakan", "dilakukan", "dimana", "
            dimanakah", "dimanapun", "dini", "diri", "dirinya", "disanalah", "disebut", "disini", "disinilah", "disitulah", "dll", "dong", "
            dulu", "e", "enggak", "enggaknya", "entah", "entahlah", "f", "g", "guna", "h", "hadap", "hai", "hal", "halo", "hampir", "
            hanya", "hanyalah", "harus", "haruslah", "harusnya", "hendak", "hendaklah", "hendaknya", "hingga", "i", "ia", "ialah", "
            ibarat", "ingin", "inginkah", "inginkan", "ini", "inikah", "inilah", "itu", "itukah", "itulah", "j", "jadi", "jangan", "
            jangankan", "janganlah", "jarang", "jauh", "jika", "jikalau", "juga", "justru", "k", "kadang", "kala", "kalau", "
            kalau-kalau", "kalaulah", "kalaupun", "kelapa", "kali", "kalian", "kami", "kamilah", "kamu", "kamulah", "kan", "kapan", "
            kapankah", "kapan-kapan", "kapanpun", "karena", "karenanya", "ke", "ke-", "kebanyakan", "kecil", "kecuali", "keduanya", "
            kemari", "kemudian", "kemungkinan", "kenapa", "kendati", "kendatipun", "kepada", "kerja", "kepadanya", "kesana", "
            keseluruhan", "kesini", "ketika", "khusus", "khususnya", "kini", "kinilah", "kira", "kiranya", "kita", "kitalah", "kok", "
            kurang", "l", "lagi", "lagian", "lah", "lain", "lainnya", "laksana", "laku", "lalu", "lama", "lamanya", "lanjut", "
            lantaran", "lantas", "lawan", "layak", "lebih", "lewat", "lowongan", "work", "lowong", "luar", "m", "macam", "maka", "
            makanya", "makin", "malah", "malahan", "mampu", "mampukah", "mana", "manakala", "manalagi", "manapun", "
            masalah", "masih", "masihkah", "masing", "masing-masing", "masyarakat", "mau", "maupun", "melainkan", "melakukan", "melalui", "
            melewati", "memang", "mempunyai", "menjadi", "menjelang", "menuju", "menurut", "
            menyeluruh", "mereka", "merekalah", "merupakan", "meski", "meskipun", "mestinya", "misal", "misalnya", "mudah-mudahan", "
            mula", "mungkin", "mungkinkah", "n", "nah", "naik", "nampaknya", "namun", "nanti", "nantinya", "non", "nya", "
            nyaris", "o", "oh", "oleh", "olehnya", "orang", "p", "pada", "pengalam", "padahal", "padanya", "paling", "panjang", "
            pelamar", "plastik", "pantas", "para", "pasti", "pastilah", "pemerintah", "per", "percuma", "perlu", "perlunya", "pernah", "
            pernah", "persis", "perhatikan", "posisi", "pula", "pun", "punya", "pekerjaan", "pt", "q", "r", "relatif", "rupa", "
            rupanya", "s", "srt", "saat", "saatnya", "saja", "sajalah", "salam", "saling", "sama", "rp", "sama-sama", "sambil", "
            sampai", "sampai-sampai", "samping", "sana", "sang", "sangat", "sangatlah", "satupun", "saya", "sayalah", "sayangnya", "
            se", "seakan", "seakan-akan", "seandainya", "sebab", "sebabnya", "sebagai", "sebagaimana", "sebagainya", "sebaiknya", "
            sebaliknya", "sebanyak", "sebegini", "sebegitu", "sebelah", "sebelum", "sebelumnya", "sebenarnya", "seberang", "seberapa", "
            sebetulnya", "sebisanya", "sebuah", "sebut", "secara", "sedang", "sedangkan", "sedari", "sedemikian", "sedikit", "sedikitnya", "
            segala", "segalanya", "segera", "sehabis", "seharusnya", "sehingga", "sederajat", "sehubungan", "sejak", "sejauh", "Sejenak", "
            sekadar", "sekali", "sekalian", "sekaligus", "sekali-kali", "sekalipun", "sekarang", "sekaranglah", "sekeliling", "seketika", "
            sekiranya", "sekitar", "sekitarnya", "seksama", "sela", "selagi", "selain", "selaku", "selalu", "selama", "selama-lamanya", "
            selamanya", "selanjutnya", "selesai", "seluruh", "seluruhnya", "semacam", "semakin", "semasa", "semasih", "semaunya", "
            sembari", "semenjak", "sementara", "sempat", "semua", "semuanya", "semula", "sendiri", "sendirinya", "seolah", "seolah-olah", "
            seorang", "sepanjang", "sepantasnya", "sepantasnyalah", "sepenuhnya", "seperti", "sepertinya", "serasa", "serasa-rasa", "
            seraya", "sering", "seringkali", "Seringnya", "serta", "serupa", "sesaat", "sesama", "sesegera", "sesekali", "seseorang", "
            sesuai", "sesuatu", "sesuatunya", "sesudah", "sesudahnya", "sesungguhnya", "setelah", "seterusnya", "setiap", "setidaknya", "
            setidak-tidaknya", "seumpama", "seusai", "sewaktu", "si", "siapa", "siapakah", "siapapun", "sih", "silahkan", "silakan", "
            sini", "sinilah", "suatu", "sudah", "Sudahkah", "sudahlah", "sungguh", "sungguhpun", "sungguh-sungguh", "supaya", "t", "
            tadi", "tadinya", "tak", "tambah", "tambahan", "tampaknya", "tanpa", "tapi", "tatkala", "telah", "teliti", "tempat", "
            tentang", "tentu", "tentulah", "tentunya", "terakhir", "terbaik", "terdiri", "terhadap", "terhadapnya", "teristimewa", "
            terjadi", "terkadang", "terlalu", "terlebih", "tersebut", "tersebutlah", "tertentu", "terus", "terutama", "tetap", "tetapi", "
            tiap", "tiba-tiba", "tidak", "tidakkah", "tidaklah", "tinggi", "tinimbang", "toh", "turun", "u", "umpamanya", "untuk", "v", "
            via", "w", "waduh", "wah", "wahai", "waktu", "walau", "walaupun", "wong", "x", "y", "ya", "yaitu", "yakni", "yang", "z", "
            jakarta", "tanggerang", "bandung", "bekasi", "jawa", "dki", "slta", "memastikan", "smp", "sma", "domisili", "lihat", "barat", "
            klip", "tahun", "bulan", "tanggal", "melihat", "gaji", "nasik", "mengendalik", "metodologi", "proses", "mengkoor", "deskripsi", "
            mengelola", "garis", "ga", "ng", "date", "can", "degree", "memiliki", "pengalaman", "minimal", "bidang", "merancang", "
            kebutuhan", "perusahaan", "mengatur", "merancang", "data", "berkoordinasi", "memahami", "konsep");
                foreach ($stopwords as &$word) {
                    $word = '/\b' . preg_quote($word, '/') . '\b/';
                }
                $hasil_token =  preg_replace($stopwords, '', $hasil_token);
                if($hasil_token !='')
                {
                    $data = array(  
                        'id_soal'=> $id_soal,
                        'token'=> $hasil_token,
                    );
                    $masukkan_token = $this->soal_ujian_model->Simpan('token', $data);
                }
            } 
        }
        //==== STEMMING NAZIEF-ADRIANI DAN PERHITUNGAN NILAI TF==== //
        $this->db->query('TRUNCATE stemming;');
        $this->db->query('TRUNCATE tf_idf;');
        $ambil_token= $this->db->query('SELECT * FROM token;');
        foreach ($ambil_token->result_array() as $row) {
            $id_soal     = $row['id_soal'];
            $jawab       = $row['token'];
            $cekKata     = $this->stemming($jawab);
            $data = array(  
                'id_soal'=> $id_soal,
                'term'=> $cekKata,
                );
            $masukkan_term = $this->soal_ujian_model->Simpan('stemming', $data);
            $hasil_stem = $cekKata;

            // hitung nilai tf
            $hitung_tf_idf = $this->tf($id_soal, $hasil_stem);
        }

        //==== PERHITUNGAN NILAI TF IDF==== //
        $ambil_jumlah_id_soal= $this->db->query('SELECT DISTINCT id_soal FROM soal_ujian;');
        $total_soal = $ambil_jumlah_id_soal->num_rows(); // hitung jumlah id_soal
        echo "$total_soal<br>";

        $ambil_tf_idf= $this->db->query('SELECT * FROM tf_idf;');
        foreach ($ambil_tf_idf->result_array() as $row) {
            $id       = $row['id'];
            $term     = $row['term'];
            $id_soal  = $row['id_soal'];
            $tf       = $row['jumlah_kata'];
            //berapa jumlah dokumen yang mengandung term tersebut?, N
            $ambil_jumlah_kata= $this->db->query("SELECT COUNT(*) as DF FROM tf_idf  WHERE term = '$term'");
            foreach ($ambil_jumlah_kata->result_array() as $row) {
                $DF       = $row['DF'];
                // hitung tf idf
                $idf = log10($total_soal/$DF);
                $nilai_tf_idf = $tf * log10($total_soal/$DF);
                $get_tf_idf = round($nilai_tf_idf,5);

                //echo "TF = $tf | DF: $DF; IDF: $idf | TF_IDF: $nilai_tf_idf<br>";
                // masukkan ke tabel tf_idf
                $masukkan_jumlah= $this->db->query("UPDATE tf_idf SET nilai_tf_idf = $get_tf_idf WHERE id = $id");
                //echo "$get_tf_idf <br>";
            }
        }
        $this->session->set_flashdata("message2", "Text Mining Berhasil Dilakukan");
        redirect(site_url('soal_ujian')); 

    }

    //fungsi perhitungan tf
    function tf($id_soal, $kata){ 
        $tampung_id_soal = $id_soal;
        $tampung_kata    = $kata;
        $arr_kata        = explode (" ",$tampung_kata);

       //echo "$arr_kata <br>";
        foreach ($arr_kata as $j => $value){                         
            //hanya jika Term tidak null atau tidak kosong                        
            if ($arr_kata[$j] != "")
            { 
                $cek_jumlah= $this->db->query("SELECT jumlah_kata FROM tf_idf WHERE term = '$arr_kata[$j]' AND id_soal = $id_soal;");
                $jumlah = $cek_jumlah->num_rows();

                //jika sudah ada id_soal dan term tersebut, naikkan 
                Count (+1);
                if ($jumlah > 0) {  
                     foreach ($cek_jumlah->result_array() as $row) {
                        $count    = $row['jumlah_kata'];
                        $count++;
                        $masukkan_jumlah= $this->db->query("UPDATE tf_idf SET jumlah_kata = $count WHERE term = '$arr_kata[$j]' AND id_soal = $id_soal");
                    }
                } 
                else{
                    $masukkan_jumlah2= $this->db->query("INSERT INTO tf_idf (id,term, id_soal, jumlah_kata, nilai_tf_idf) VALUES ('','$arr_kata[$j]', $id_soal, 1,'')");
                } 
            }
        }
    }
}