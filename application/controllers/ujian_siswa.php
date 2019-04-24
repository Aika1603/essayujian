<?php ob_start();
class ujian_siswa extends CI_Controller {
    function __construct(){
        parent::__construct();
        $this->load->model('ujian_siswa_model');
        $this->load->model('soal_ujian_model');
    }

    function index(){
        $this->security_model->getsecurity();
        $nis=$this->session->userdata('nis');

        $jumlah_soal = $this->db->query("SELECT * from soal_ujian;");
        $total_soal  = $jumlah_soal->num_rows();

        $jumlah_soal = $this->db->query("SELECT * from hasil_akhir_ujian WHERE nis = '$nis';");
        $total_jawaban  = $jumlah_soal->num_rows();

        // jika sama maka soal tidak ditampilakan
        if($total_soal == $total_jawaban){
            $this->load->view('siswa/hitung_nilai');   
        }else{
            $this->load->database();
            $query1 = "SELECT waktu_ujian_menit as menit from set_ujian";
            $data['query1'] = $this->db->query($query1);

            $nis=$this->session->userdata('nis');

            //cek jumlah soal
            $ambil_jumlah_soalnya = $this->db->query("SELECT * from soal_ujian;");
            $total_soalnya = $ambil_jumlah_soalnya->num_rows();

            //cek id terakhir dari nis tersebut
            $data_id = $this->db->query("SELECT id from random_soal where nis = '$nis' ORDER by id DESC limit 1;");
            foreach ($data_id->result_array() as $row) 
            {
                $id     = $row['id'];
            };
            $id_soal = ($id + 1) - $total_soalnya;

            $ambil_data3 = $this->db->query("SELECT soal_ujian.`id_soal`, soal_ujian.`pertanyaan`, hasil_akhir_ujian.`kunci_jawaban` FROM soal_ujian JOIN hasil_akhir_ujian ON soal_ujian.`id_soal` = hasil_akhir_ujian.`id_soal` WHERE nis='$nis' ORDER BY id_soal ASC;");
            $total_data = $ambil_data3->num_rows();
            $cek_id_soal = $id_soal + ($total_data);

            //ambil soal
            $query2 = ("SELECT random_soal.`id`, soal_ujian.`id_soal`, soal_ujian.`pertanyaan` FROM soal_ujian JOIN random_soal ON random_soal.`no_soal` = soal_ujian.`id_soal` WHERE random_soal.`id`= '$cek_id_soal';");
            $data['query2'] = $this->db->query($query2);
            
            $data['detik']='60';
            
            
            $data['hasil_nilai'] ='';
            $data['content'] ='siswa/list_ujian';
            $data['action']  ='ujian_siswa/cek_ujian';
            $this->load->view('siswa/index',$data);    
        } 
    }

    function close($input, $key){
        $closest = "";
        $terpendek = -1;
        $word = preg_split('/\s+/',$key);
        $in = preg_split('/\s+/',$input);

        for($x = 0; $x < sizeof($in); $x++){
            for($y = 0; $y < sizeof($word); $y++){
                $lev = levenshtein($input[$x], $word[$y]);
                if ($lev == 0) {
                    $closest = $word[$y];
                    $terpendek = 0;
                    break;
                }
                if ($lev <= $terpendek || $terpendek < 0) {
                    $closest = $word[$y];
                    $terpendek = $lev;
                }
            }
            
        }
        return $closest;
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
                $cek_jumlah= $this->db->query("SELECT jumlah_kata FROM tf_idf_jawaban WHERE term = '$arr_kata[$j]' AND id_soal = $id_soal;");
                $jumlah = $cek_jumlah->num_rows();

                //jika sudah ada id_soal dan term tersebut, naikkan 
                Count (+1);
                if ($jumlah > 0) {  
                     foreach ($cek_jumlah->result_array() as $row) {
                        $count    = $row['jumlah_kata'];
                        $count++;
                        $masukkan_jumlah= $this->db->query("UPDATE tf_idf_jawaban SET jumlah_kata = $count WHERE term = '$arr_kata[$j]' AND id_soal = $id_soal");
                    }
                } 
                else{
                    $masukkan_jumlah2= $this->db->query("INSERT INTO tf_idf_jawaban (id,term, id_soal, jumlah_kata, nilai_tf_idf) VALUES ('','$arr_kata[$j]', $id_soal, 1,'')");
                } 
            }
        }
    }

    public function proses() 
    {    
        //$this->db->query('TRUNCATE hasil_ujian;');
        $jumlah_soal= $this->db->query("SELECT id_soal FROM soal_ujian");
        $jumlah = $jumlah_soal->num_rows();

        $this->security_model->getsecurity();   
        $nis=$this->session->userdata('nis');

        $this->db->query('TRUNCATE token;');
        for ($i=1; $i<=$jumlah; $i++){
            $id_soal     = $_POST['id_soal'];
            $isi_jawaban = $_POST['isi_jawaban'];
            //id nomor dan jawaban soal
            $nomor   = $id_soal[$i];
            $jawaban = $isi_jawaban[$i];

            if($nomor != 0 AND $jawaban !=""){
                $masukkan_hasil_ujian= $this->db->query("INSERT INTO hasil_ujian (id_ujian, nis, id_soal, kunci_jawaban) VALUES ('','$nis', '$nomor', '$jawaban')");
            }

            $nis            = $nis;
            $id_soal        = $nomor;
            $kunci_jawaban  = $jawaban;
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
                    $masukkan_token = $this->ujian_siswa_model->Simpan('token', $data);
                }
            } 
        }
        $nomor_soal     = $id_soal;

        //==== STEMMING NAZIEF-ADRIANI DAN PERHITUNGAN NILAI TF==== //
        $this->db->query('TRUNCATE stemming;');
        $this->db->query('TRUNCATE tf_idf_jawaban;');
        $ambil_token= $this->db->query('SELECT * FROM token;');
        foreach ($ambil_token->result_array() as $row) {
            $id_soal     = $row['id_soal'];
            $jawab       = $row['token'];
            $cekKata     = $this->stemming($jawab);
            $data = array(  
                'id_soal'=> $id_soal,
                'term'=> $cekKata,
                );
            $masukkan_term = $this->ujian_siswa_model->Simpan('stemming', $data);
            $hasil_stem = $cekKata;

            // hitung nilai tf
            $hitung_tf_idf = $this->tf($id_soal, $hasil_stem);
        }

        //==== PERHITUNGAN NILAI TF IDF==== //
        $ambil_jumlah_id_soal= $this->db->query('SELECT DISTINCT id_soal FROM soal_ujian;');
        $jumlah_id_soal = $ambil_jumlah_id_soal->num_rows(); // hitung jumlah id_soal

        $ambil_tf_idf= $this->db->query('SELECT * FROM tf_idf_jawaban;');
        foreach ($ambil_tf_idf->result_array() as $row) {
            $id       = $row['id'];
            $term     = $row['term'];
            $id_soal  = $row['id_soal'];
            $tf       = $row['jumlah_kata'];
            //berapa jumlah dokumen yang mengandung term tersebut?, N
            $ambil_jumlah_kata= $this->db->query("SELECT COUNT(*) as N FROM tf_idf_jawaban  WHERE term = '$term'");
            foreach ($ambil_jumlah_kata->result_array() as $row) {
                $N       = $row['N'];
                // hitung tf idf
                $nilai_tf_idf = $tf * log10($jumlah_id_soal/$N);
                $get_tf_idf = round($nilai_tf_idf,4);
                // masukkan ke tabel tf_idf
                $masukkan_jumlah= $this->db->query("UPDATE tf_idf_jawaban SET nilai_tf_idf = $get_tf_idf WHERE id = $id");
            }
        }
        //== PERHITUNGAN NILAI COSINE SIMILARITY ==//
        //==== Perhitungan Dot Product==== //
        $this->db->query("TRUNCATE tampung");
        $ambil_id_soal= $this->db->query('SELECT DISTINCT id_soal FROM tf_idf;'); //mengambil data id_soal
        foreach ($ambil_id_soal->result_array() as $row) {
            $id_soal       = $row['id_soal'];

            $ambil_tf_idf_jawaban= $this->db->query('SELECT * FROM tf_idf_jawaban;');
            foreach ($ambil_tf_idf_jawaban->result_array() as $row) {
                $id                    = $row['id'];
                $term                  = $row['term'];
                $nilai_tf_idf_jawaban  = $row['nilai_tf_idf']; 

                $ambil_tf_idf= $this->db->query("SELECT nilai_tf_idf FROM tf_idf WHERE term = '$term' AND id_soal= $id_soal;");
                foreach ($ambil_tf_idf->result_array() as $row) {
                    $nilai_tf_idf   = $row['nilai_tf_idf'];
                    $dot_product = $nilai_tf_idf_jawaban*$nilai_tf_idf;
                    $bulatkan = round($dot_product,5);
                    //echo "ID Soal : $id_soal | Nilai Dot Produtc: $dot_product <br>";
                    $masukkan_dot_product= $this->db->query("INSERT INTO tampung VALUES ('$id_soal','$bulatkan')");
                }
            }echo "<br>";
        } 
        //jumlahkan total dot product
        $this->db->query("TRUNCATE sum_dot_product");
        $ambil_id_soal2= $this->db->query('SELECT DISTINCT id_soal FROM tampung;');        
        foreach ($ambil_id_soal2->result_array() as $row) {
            $id_soal      = $row['id_soal'];

            $ambil_jumlah= $this->db->query("SELECT SUM(jumlah) AS jumlah FROM tampung WHERE id_soal= '$id_soal'");       
            foreach ($ambil_jumlah->result_array() as $row) {
                $jumlah     = $row['jumlah'];
                $bulatkan2  = round($jumlah,5);
                $masukkan_dot_product2= $this->db->query("INSERT INTO sum_dot_product VALUES ('$id_soal','$bulatkan2')");
            }
        }


        //==== Perhitungan Cross Product==== //
        // pemangkatan //
        $this->db->query("TRUNCATE tampung");
        $ambil_id_soal3= $this->db->query('SELECT DISTINCT id_soal FROM tf_idf;');        
        foreach ($ambil_id_soal3->result_array() as $row) {
            $id_soal      = $row['id_soal'];
            
            $ambil_tf_idf2= $this->db->query("SELECT nilai_tf_idf FROM tf_idf WHERE id_soal = '$id_soal'");
            foreach ($ambil_tf_idf2->result_array() as $row) {
                $nilai_tf_idf     = $row['nilai_tf_idf'];
                //pangkatkan nilai tf idf untuk soal
                $pangkatkan = $nilai_tf_idf*$nilai_tf_idf;
                $masukkan_pangkat=$this->db->query("INSERT INTO tampung VALUES ('$id_soal','$pangkatkan')");
            }
        }
        // penjumlahan hasil pemangkatan //
        $this->db->query("TRUNCATE sum_cross_product");
        $ambil_id_soal4= $this->db->query('SELECT DISTINCT id_soal FROM tampung;');        
        foreach ($ambil_id_soal4->result_array() as $row) {
            $id_soal      = $row['id_soal'];

            $ambil_jumlah2= $this->db->query("SELECT SUM(jumlah) AS jumlah FROM tampung WHERE id_soal= '$id_soal'");
            foreach ($ambil_jumlah2->result_array() as $row) {
                $jumlah     = $row['jumlah'];
                $bulatkan2  = round($jumlah,5);
                //masukkan hasil penjulahan cross product
                $masukkan_sum=$this->db->query("INSERT INTO sum_cross_product VALUES ('$id_soal','$bulatkan2')");
            }
        }
        // pemangkatan untuk jawaban
        $this->db->query("TRUNCATE tampung");
        $ambil_data2= $this->db->query('SELECT term FROM tf_idf_jawaban ORDER BY id');        
        foreach ($ambil_data2->result_array() as $row) {
            $term         = $row['term'];
            
            $ambil_tf_idf3= $this->db->query("SELECT nilai_tf_idf FROM tf_idf_jawaban WHERE term = '$term'");
            foreach ($ambil_tf_idf3->result_array() as $row) {
                $nilai_tf_idf     = $row['nilai_tf_idf'];
                //pangkatkan nilai tf idf untuk soal
                $pangkatkan2 = $nilai_tf_idf*$nilai_tf_idf;
                $masukkan_pangkat=$this->db->query("INSERT INTO tampung VALUES ('','$pangkatkan2')");
            }
        }
        // penjumlahan hasil pemangkatan //
        $this->db->query("TRUNCATE sum_jawaban_cross_product");
        $ambil_jumlah2= $this->db->query("SELECT SUM(jumlah) AS jumlah FROM tampung");
        foreach ($ambil_jumlah2->result_array() as $row) {
            $jumlah     = $row['jumlah'];
            $bulatkan3  = round($jumlah,5);
            //masukkan hasil penjulahan cross product
            $masukkan_sum=$this->db->query("INSERT INTO sum_jawaban_cross_product VALUES ('$bulatkan3')");
        }
        // Perkalian Cross Product //
        $this->db->query("TRUNCATE cross_product");
        $ambil_jumlah4= $this->db->query("SELECT id_soal, cross_product FROM sum_cross_product");
        foreach ($ambil_jumlah4->result_array() as $row) {
            $id_soal       = $row['id_soal'];
            $cross_product = $row['cross_product'];
            $ambil_jumlah5= $this->db->query("SELECT jawaban_cross_product FROM sum_jawaban_cross_product");
            foreach ($ambil_jumlah5->result_array() as $row) {
                $jawaban_cross_product  = $row['jawaban_cross_product'];
                $kalikan = $cross_product * $jawaban_cross_product;
                $akarkan = sqrt($kalikan);
                $bulatkan4 = round($akarkan,5);
                $masukkan_akar=$this->db->query("INSERT INTO cross_product VALUES ('$id_soal','$bulatkan4')");
            }
        }

        //==== Hitung Cosine Similarity ==== //
        $this->db->query("TRUNCATE hasil_cosine");
        $ambil_jumlah5= $this->db->query("SELECT id_soal, dot_product FROM sum_dot_product");
        foreach ($ambil_jumlah5->result_array() as $row) {
            $id_soal       = $row['id_soal'];
            $dot_product   = $row['dot_product'];
            $ambil_hasil_akar= $this->db->query("SELECT hasil_akar FROM cross_product WHERE id_soal = '$id_soal'");
            foreach ($ambil_hasil_akar->result_array() as $row) {
                $hasil_akar      = $row['hasil_akar'];
                $cosine          = @$dot_product / $hasil_akar;
                $bulatkan5       = round($cosine,5);
                //if($no_soal == )
                $masukkan_cosine=$this->db->query("INSERT INTO hasil_cosine VALUES ('$nis','$id_soal','$bulatkan5')");
            }
        }

        // === Tambahkan ke Nilai Cosine Ke Tabel Hasil Akhir Ujian === //
        $ambil_jumlah6= $this->db->query("SELECT * FROM hasil_ujian");
        foreach ($ambil_jumlah6->result_array() as $row) {
            $id_ujian  = $row['id_ujian'];
            $nis       = $row['nis'];
            $id_soal   = $row['id_soal'];
            $kunci_jawaban   = $row['kunci_jawaban'];

            $get_cosine= $this->db->query("SELECT jumlah from hasil_cosine WHERE nis= '$nis' and id_soal ='$id_soal'");
            $total = $get_cosine->num_rows();

            foreach ($get_cosine->result_array() as $row) {
                $jumlah   = $row['jumlah'];
            }
            if($total == 0){
                $tambah= $this->db->query("INSERT into hasil_akhir_ujian values ('', '$nis', '$id_soal','$kunci_jawaban', 0)");
            }else{
                $tambah= $this->db->query("INSERT into hasil_akhir_ujian values ('', '$nis', '$id_soal','$kunci_jawaban','$jumlah')");
            }
        }
        $this->db->query("TRUNCATE hasil_ujian");
        redirect(site_url('ujian_siswa'));
    }

    // ============================ PERHITUNGAN NILAI AKHIR ========================//
    function hitung_nilai(){ 

        $nis=$this->session->userdata('nis');
        $ambil_jumlah_soal= $this->db->query("SELECT id_soal FROM soal_ujian");
        $total_soal = $ambil_jumlah_soal->num_rows(); //jumlah soal
        //ambil  nilai cosine
        $ambil_total_cosine = $this->db->query("SELECT sum(nilai_cosine) as nilai_cosine from hasil_akhir_ujian WHERE nis = '$nis'");
        foreach ($ambil_total_cosine->result_array() as $row) {
            //hitung nilai akhir
            $nilai_cosine   = $row['nilai_cosine'];
            $nilai_akhir    = ($nilai_cosine * 100) / $total_soal;
            $bulatkan6      = round($nilai_akhir);
        }
        $tambah= $this->db->query("INSERT into nilai_akhir values ('$nis', '$bulatkan6')");
        redirect(site_url('home/log_siswa'));
    }
}
?>