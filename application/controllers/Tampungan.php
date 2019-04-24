<?php
function praproses($jawab){

		//TOKENIZING / FILTERING ==================================================================
		$ttext=trim($jawab);
		$ftext=preg_replace('/[^a-zA-Z0-9 ]/',' ',$ttext);
		//CASE FOLDING ============================================================================
		$ctext=strtolower($ftext);
		//STOPWORD REMOVAL ========================================================================
		$otext=$this->stopword($ctext);//hilangkan kata yg kurang penting
		$wtext=preg_replace("/\b(\w+)\s*\\1\b/i", "$1", $otext);//hilangkan kata yg berulang (tanpa spasi)
		$stext=implode(' ', array_unique(explode(' ', $wtext)));//hilangkan kata yg berulang (dengan spasi)
        return $p_jawab = preg_split('/\s+/',$stext);
	}
	
	function cek_ujian(){
		
		$this->security_model->getsecurity();	
		$sisa_menit=$this->input->post('waktu_menit');
		$sisa_detik=$this->input->post('waktu_detik');
		$no_soal=$this->input->post('no_soal');
		$data['menit']=$sisa_menit;
		$data['detik']=$sisa_detik;
		$kunci_jawaban=$this->input->post('kunci_jawaban');
		$jawab=$this->input->post('isi_jawaban');

		//================================== EXPLODE Kunci Jawaban =================================
		$stem_kunci = $this->praproses($kunci_jawaban);
       	$stemm = "";
		$Kunci = "";
		for($x = 0; $x < sizeof($stem_kunci); $x++){
			$stemm = $this->stemming($stem_kunci[$x]);
			if($stemm == null){
				$Kunci .= preg_replace('{(.)\1+}','$1',$stem_kunci[$x]);
			}else{
				$Kunci .= $stemm;	
			}
		}
		//menghilangkan kata yg berulang pada kunci jawaban
		$kunci_Jawab = preg_replace("/\b(\w+)\s*\\1\b/i", "$1", $Kunci);
		//=========================================================================================


		//==================================== EXPLODE Jawaban ====================================
		$p_jawab = $this->praproses($jawab);
		$jawabanInsert = "";
		$stemm = "";
		for($x = 0; $x < sizeof($p_jawab); $x++){
			//$stemm = $this->stemming($this->close($p_jawab[$x] , $kunci_Jawab));
			$stemm = $this->stemming($p_jawab[$x]);
			$stemm = preg_replace('{(.)\1+}','$1',$stemm);//menghilangkan teks yg sama
			$jawabanInsert .= $stemm;	
		}
		//menghilangkan kata yg berulang pada jawaban
		$jawabanIns=preg_replace("/\b(\w+)\s*\\1\b/i", "$1", $jawabanInsert);
		//=========================================================================================
    
		//hasil detail jawaban
		$hasil = $this->input->post('hasil_nilai');
		$str1 = trim($jawabanIns);//hilangkan whitespace s1
		$s1 = strlen($str1); // menghitung s1
		$str2 = trim($kunci_Jawab);//hilangkan whitespace s2
		$s2 = strlen($str2); // menghitung s2
		$similar = similar_text($jawabanIns, $kunci_Jawab); //hitung m
		 
		$dj = $this->jaro($kunci_Jawab, $jawabanIns); //hitung jaro distance
		$dw = $this->jaroWinkler1($kunci_Jawab, $jawabanIns); //hitung winkler distance
		$nilai = $this->jaroWinkler($kunci_Jawab, $jawabanIns); //hitung nilai akhir

		//simpan detail jawaban
		$id=$this->session->userdata('nis');
	    $data = array(
	      'id_detail' =>'',
	      'nis' => $id,
		  'jawaban' => $jawab,
		  'hasil_stemming' => $jawabanIns,
		  'kunci_jawaban' => $kunci_Jawab,
		  'nilai_winkler' => $dw,
		  'nilai_jaro' => $dj,
		  'string_satu' => $s1,
		  'string_dua' => $s2,
		  //'lens' => $len,
		  'm' => $similar,
		  'na' => $nilai,
		 );
		$this->ujian_siswa_model->insert_detail($data);
	    $data['hasil_nilai']=$hasil+$nilai;

		//pertanyaan
		$no=++$no_soal;
		$data['no_soal']=$no;
		$query=$this->ujian_siswa_model->get_soal(--$no);
		if ($query->num_rows()>0){
	        foreach ($query->result() as $row){
	            $data['soal_pertanyaan']= $row->pertanyaan;
	            $data['kunci_jawaban']= $row->kunci_jawaban;
	        }
			//total soal
				$total_soal='';
				$query2=$this->ujian_siswa_model->get_total_soal();
			         	foreach ($query2->result() as $row){
			            $total_soal= $row->total;
			     	 	}
			     			if ($no > $total_soal){
			     			//masukan hasil
			     				$id=$this->session->userdata('nis');
			     				$data = array(
		                    		'nis' => $id,
		                    		'nilai_ujian' => $data['hasil_nilai'],
		                    		'waktu_ujian' => date('Y-m-d H:i:s'),
		                		);
			     			$this->ujian_siswa_model->insert_hasil($data,$id);
							$this->hasil_ujian();				
							}else{
								$data['content']='siswa/list_ujian';
								$data['action']='';
								$this->load->view('siswa/index',$data); }
	    	}else{
	    	//masukan hasil perhitungan_nilai
	     		$id=$this->session->userdata('nis');
	     		$query2=$this->ujian_siswa_model->get_total_soal();
			         foreach ($query2->result() as $row){
			            $total_soal= $row->total;
			     }
				$nilai_akhir=$data['hasil_nilai']/$total_soal;
	     		$data = array(
                    'nis' => $id,
                    'nilai_ujian' => $nilai_akhir,
                    'waktu_ujian' => date('Y-m-d H:i:s'),
                );
	     		$this->ujian_siswa_model->insert_hasil($data,$id);
	    	//tampilkan hasil 
	     		$this->hasil_ujian();
	    	}
		}

 	function waktu_stop($a,$b){
 	 	//masukan hasil
	     	$id=$this->session->userdata('nis');
			$hasil=$b;
			$no_soal=$a;
			$nilai_akhir=$hasil/$no_soal;
	     	$data = array(
                    'nis' => $id,
                    'nilai_ujian' => $nilai_akhir,
                    'waktu_ujian' => date('Y-m-d H:i:s'),
            );
	     	$this->ujian_siswa_model->insert_hasil($data,$id);
	    //tampilkan hasil
	     	redirect('ujian_siswa/hasil_ujian');
 	}

 	function hasil_ujian(){
		$this->security_model->getsecurity();
		$id=$this->session->userdata('nis');
		$nama=$this->session->userdata('username');
		$query=$this->ujian_siswa_model->get_by_id($id);
			foreach ($query->result() as $row){
			            $nilai= $row->nilai_ujian;
			            $waktu= $row->waktu_ujian;
			}
			$data = array(
					'nis' => $id,
                    'nama' => $nama,
                    'nilai_ujian' => $nilai,
                    'waktu_ujian' => $waktu,
            );
		$data['content']='siswa/list_hasil_ujian';
		$this->load->view('siswa/index',$data);
 	}	


	//hitung Jaro Distance
	 function jaro($string1, $string2){

		$string1_len = strlen($string1);
		$string2_len = strlen($string2);
		$distance = (int) floor ((max($string1_len,$string2_len))/2)-1;
		$commons1 = $this->commonCharacters( $string1, $string2, $distance );
		$commons2 = $this->commonCharacters( $string2, $string1, $distance );
		if( ($commons1_len = strlen( $commons1 )) == 0) return 0;
		if( ($commons2_len = strlen( $commons2 )) == 0) return 0;
	//hitung transposisi
		$transpositions = 0;
		$upperBound = min($commons1_len, $commons2_len);
		for( $i = 0; $i < $upperBound; $i++){
			if( $commons1[$i] != $commons2[$i] ) 
			$transpositions++;
		}
		$transpositions /= 2.0;
	//kembalikan nilai Jaro Distance
		return (($upperBound/($string1_len) + $upperBound/($string2_len) + ($upperBound - $transpositions)/($commons1_len)) / 3.0);
	}

	function commonCharacters( $string1, $string2, $distance ){
		$string1_len = strlen($string1);
		$string2_len = strlen($string2);
		$commonCharacters='';
		$matching=0;
		for($i=0;$i<$string1_len;$i++){
			$noMatch = True;
			for( $j= 0; $noMatch && $j < $string2_len ; $j++){
				if(($string2[$j]==$string1[$i]) && (abs($j-$i)<=$distance)){
					$noMatch = False;
					$matching++;
					$commonCharacters .= $string1[$i];
				}
			}
		}return $commonCharacters;
	}

	//hitung jarak prefix antara s1 dan s2 (max 4)
	function prefixLength( $string1, $string2, $MINPREFIXLENGTH = 4 ){
		$n = min( array( $MINPREFIXLENGTH, strlen($string1), strlen($string2) ) );
    	for($i = 0; $i < $n; $i++){
			if( $string1[$i] != $string2[$i] ){
			return $i;
			}
		}return $n;
	}

	//hitung nilai akhir
	function jaroWinkler($string1, $string2){
		$PREFIXSCALE = 0.1;
		$string1 = strtolower($string1);
		$string2 = strtolower($string2);
		$jaroDistance = $this->jaro( $string1, $string2 );
		$prefixLength = $this->prefixLength( $string1, $string2 );
		$score = round(($jaroDistance + ($prefixLength * $PREFIXSCALE * (1.0 - $jaroDistance)))*100,2);	
 		return $score;
	}

	// function prefixLength1($string1, $string2){
	// 	$MINPREFIXLENGTH = 4;
	// 	$n = min( array( $MINPREFIXLENGTH, strlen($string1), strlen($string2) ) );
 //    	for($i = 0; $i < $n; $i++){
	// 		if( $string1[$i] != $string2[$i] ){
	// 		return $i;
	// 		}
	// 	}return $n;
	// }

	//detail winkler distance
	function jaroWinkler1($string1, $string2){
		$PREFIXSCALE = 0.1;
		$string1 = strtolower($string1);
		$string2 = strtolower($string2);
		$jaroDistance = $this->jaro( $string1, $string2 );
		$prefixLength = $this->prefixLength( $string1, $string2 );
		$score = round(($jaroDistance + ($prefixLength * $PREFIXSCALE * (1.0 - $jaroDistance))),2);
 		return $score;
	}

	function stopword($text){
  		$idStopword= Array(' a ',' ada ',' adalah ',' adanya ',' adapun ',' an ',' agak ',' agaknya ',' kordinasi ',' agar ',' 
  			akan ',' akankah ',' akhir ',' asa ',' akhirnya ',' akibat ',' akibatnya ',' aku ',' akulah ',' amat ',' amatlah ',' 
  			anda ',' andai ',' andaikan ',' andaikata ',' andalah ',' antar ',' antara ',' antaranya ',' apa ',' apaan ',' apabila ',' 
  			apakah ',' apalagi ',' apapun ',' apatah ',' asal ',' asalkan ',' atas ',' atau ',' ataukah ',' ataupun ',' b ',' bagai ',' 
  			bagaikan ',' bagaimana ',' bangsa ',' bagaimanakah ',' bagaimanapun ',' bagi ',' bahkan ',' bahwa ',' bahwasanya ',' baik ',' 
  			baiklah ',' balik ',' banyak ',' barangkali ',' baru ',' bawah ',' bebas ',' beberapa ',' begini ',' beginian ',' 
  			beginikah ',' beginilah ',' begitu ',' begitukah ',' begitulah ',' begitupun ',' belakang ',' belum ',' belumlah ',' 
  			benar ',' benar-benar ',' berapa ',' berapakah ',' berapalah ',' berapapun ',' berbagai ',' berbeda ',' berhubung ',' 
  			berikut ',' berikutnya ',' berkali-kali ',' berkat ',' bermacam ',' bermacam-macam ',' bersama ',' bersama-sama ',' besar ',' 
  			beserta ',' betul ',' betulkah ',' biar ',' biarpun ',' biasa ',' biasanya ',' bila ',' bilakah ',' bilamana ',' bisa ',' bisakah ','
  			boleh ',' bolehkah ',' bolehlah ',' buat ',' bukan ',' bukankah ',' bukanlah ',' bukannya ',' c ',' cenderung ',' cermat ',' contoh ',' 
  			cukup ',' cuma ',' d ',' dahulu ',' dalam ',' dan ',' dapat ',' dari ',' darimana ',' daripada ',' dekat ',' deng ',' demi ',' demikian ','
  			demikianlah ',' dengan ',' depan ',' di ',' dia ',' dialah ',' diantara ',' diantaranya ',' dikarenakan ',' dilakukan ',' dimana ',' 
  			dimanakah ',' dimanapun ',' dini ',' diri ',' dirinya ',' disanalah ',' disebut ',' disini ',' disinilah ',' disitulah ',' dll ',' dong ',' 
  			dulu ',' e ',' enggak ',' enggaknya ',' entah ',' entahlah ',' f ',' g ',' guna ',' h ',' hadap ',' hai ',' hal ',' halo ',' hampir ',' 
  			hanya ',' hanyalah ',' harus ',' haruslah ',' harusnya ',' hendak ',' hendaklah ',' hendaknya ',' hingga ',' i ',' ia ',' ialah ',' 
  			ibarat ',' ingin ',' inginkah ',' inginkan ',' ini ',' inikah ',' inilah ',' itu ',' itukah ',' itulah ',' j ',' jadi ',' jangan ',' 
  			jangankan ',' janganlah ',' jarang ',' jauh ',' jika ',' jikalau ',' juga ',' justru ',' k ',' kadang ',' kala ',' kalau ',' 
  			kalau-kalau ',' kalaulah ',' kalaupun ',' kelapa ',' kali ',' kalian ',' kami ',' kamilah ',' kamu ',' kamulah ',' kan ',' kapan ',' 
  			kapankah ',' kapan-kapan ',' kapanpun ',' karena ',' karenanya ',' ke ',' ke- ',' kebanyakan ',' kecil ',' kecuali ',' keduanya ',' 
  			kemari ',' kemudian ',' kemungkinan ',' kenapa ',' kendati ',' kendatipun ',' kepada ','kerja',' kepadanya ',' kesana ',' 
  			keseluruhan ',' kesini ',' ketika ',' khusus ',' khususnya ',' kini ',' kinilah ',' kira ',' kiranya ',' kita ',' kitalah ',' kok ',' 
  			kurang ',' l ',' lagi ',' lagian ',' lah ',' lain ',' lainnya ',' laksana ',' laku ',' lalu ',' lama ',' lamanya ',' lanjut ',' 
  			lantaran ',' lantas ',' lawan ',' layak ',' lebih ',' lewat ',' lowongan ',' work ',' lowong ',' luar ',' m ',' macam ',' maka ',' 
  			makanya ',' makin ',' malah ',' malahan ',' mampu ',' mampukah ',' mana ',' manakala ',' manalagi ',' manapun ',' 
  			masalah ',' masih ',' masihkah ',' masing ',' masing-masing ',' masyarakat ',' mau ',' maupun ',' melainkan ',' melakukan ',' melalui ',' 
  			melewati ',' memang ',' mempunyai ',' menjadi ',' menjelang ',' menuju ',' menurut ',' 
  			menyeluruh ',' mereka ',' merekalah ',' merupakan ',' meski ',' meskipun ',' mestinya ',' misal ',' misalnya ',' mudah-mudahan ',' 
  			mula ',' mungkin ',' mungkinkah ',' n ',' nah ',' naik ',' nampaknya ',' namun ',' nanti ',' nantinya ',' non ',' nya ',' 
  			nyaris ',' o ',' oh ',' oleh ',' olehnya ',' orang ',' p ',' pada ',' pengalam ',' padahal ',' padanya ',' paling ',' panjang ',' 
  			pelamar ',' plastik ',' pantas ',' para ',' pasti ',' pastilah ',' pemerintah ',' per ',' percuma ',' perlu ',' perlunya ',' pernah ',' 
  			pernah ',' persis ',' perhatikan ',' posisi ',' pula ',' pun ',' punya ',' pekerjaan ',' pt ',' q ',' r ',' relatif ',' rupa ',' 
  			rupanya ',' s ',' srt',' saat ',' saatnya ',' saja ',' sajalah ',' salam ',' saling ',' sama ',' rp ',' sama-sama ',' sambil ',' 
  			sampai ',' sampai-sampai ',' samping ',' sana ',' sang ',' sangat ',' sangatlah ',' satupun ',' saya ',' sayalah ',' sayangnya ',' 
  			se ',' seakan ',' seakan-akan ',' seandainya ',' sebab ',' sebabnya ',' sebagai ',' sebagaimana ',' sebagainya ',' sebaiknya ',' 
  			sebaliknya ',' sebanyak ',' sebegini ',' sebegitu ',' sebelah ',' sebelum ',' sebelumnya ',' sebenarnya ',' seberang ',' seberapa ',' 
  			sebetulnya ',' sebisanya ',' sebuah ',' sebut ',' secara ',' sedang ',' sedangkan ',' sedari ',' sedemikian ',' sedikit ',' sedikitnya ',' 
  			segala ',' segalanya ',' segera ',' sehabis ',' seharusnya ',' sehingga ',' sederajat ',' sehubungan ',' sejak ',' sejauh ',' Sejenak ',' 
  			sekadar ',' sekali ',' sekalian ',' sekaligus ',' sekali-kali ',' sekalipun ',' sekarang ',' sekaranglah ',' sekeliling ',' seketika ',' 
  			sekiranya ',' sekitar ',' sekitarnya ',' seksama ',' sela ',' selagi ',' selain ',' selaku ',' selalu ',' selama ',' selama-lamanya ',' 
  			selamanya ',' selanjutnya ',' selesai ',' seluruh ',' seluruhnya ',' semacam ',' semakin ',' semasa ',' semasih ',' semaunya ',' 
  			sembari ',' semenjak ',' sementara ',' sempat ',' semua ',' semuanya ',' semula ',' sendiri ',' sendirinya ',' seolah ',' seolah-olah ',' 
  			seorang ',' sepanjang ',' sepantasnya ',' sepantasnyalah ',' sepenuhnya ',' seperti ',' sepertinya ',' serasa ',' serasa-rasa ',' 
  			seraya ',' sering ',' seringkali ',' Seringnya ',' serta ',' serupa ',' sesaat ',' sesama ',' sesegera ',' sesekali ',' seseorang ',' 
  			sesuai ',' sesuatu ',' sesuatunya ',' sesudah ',' sesudahnya ',' sesungguhnya ',' setelah ',' seterusnya ',' setiap ',' setidaknya ',' 
  			setidak-tidaknya ',' seumpama ',' seusai ',' sewaktu ',' si ',' siapa ',' siapakah ',' siapapun ',' sih ',' silahkan ',' silakan ',' 
  			sini ',' sinilah ',' suatu ',' sudah ',' Sudahkah ',' sudahlah ',' sungguh ',' sungguhpun ',' sungguh-sungguh ',' supaya ',' t ',' 
  			tadi ',' tadinya ',' tak ',' tambah ',' tambahan ',' tampaknya ',' tanpa ',' tapi ',' tatkala ',' telah ',' teliti ',' tempat ',' 
  			tentang ',' tentu ',' tentulah ',' tentunya ',' terakhir ',' terbaik ',' terdiri ',' terhadap ',' terhadapnya ',' teristimewa ',' 
  			terjadi ',' terkadang ',' terlalu ',' terlebih ',' tersebut ',' tersebutlah ',' tertentu ',' terus ',' terutama ',' tetap ',' tetapi ',' 
  			tiap ',' tiba-tiba ',' tidak ',' tidakkah ',' tidaklah ',' tinggi ',' tinimbang ',' toh ',' turun ',' u ',' umpamanya ',' untuk ',' v ',' 
  			via ',' w ',' waduh ',' wah ',' wahai ',' waktu ',' walau ',' walaupun ',' wong ',' x ',' y ',' ya ',' yaitu ',' yakni ',' yang ',' z ',' 
  			jakarta ',' tanggerang ',' bandung ',' bekasi ',' jawa ',' dki ','slta' ,'memastikan',' smp ', ' sma ', 'domisili ', ' lihat ', ' barat ','
  			klip', 'tahun', 'bulan',' tanggal',' melihat ', ' gaji ', 'nasik','mengendalik',' metodologi ', ' proses ',' mengkoor ',' deskripsi ', ' 
  			mengelola ',' garis ',' ga ', ' ng ',' date ', 'can ', ' degree ', ' memiliki ', ' pengalaman ', ' minimal ', ' bidang ', 'merancang ', ' 
  			kebutuhan ',' perusahaan ', ' mengatur ',' merancang ', ' data ',' berkoordinasi', 'memahami', 'konsep');
  		$text= str_replace($idStopword,' ',$text); 
  		return $text;	
  	}

	//========================================== Stopwords =============================================
	// function cekStopword($jawab){ 
	// 	$query = $this->ujian_siswa_model->get_by_id_stopword($jawab);

	// 	if(sizeof($query->result())==1){
	// 		return true; // True jika ada
	// 	}else{
	// 		return false; // jika tidak ada FALSE
	// 	}
	// }
	// function stopwords($jawab){ 
	// 	$cek = $this->cekStopword($jawab);
	// 	if($cek == true){ // Cek Stopword dalam database
	// 		return $jawab; // Jika benar maka kata tersebut hasil tokenisasi yg merupakan kata penting dalam database
	// 	}
	// 	else{ //jika tidak ada maka kata tersebut hasil tokenisasi yg merupakan kata tidak penting dalam database
	// 		if($this->cekStopword($jawab) == false){
	// 			return $jawab;
	// 		}
	// 	}
	// }
    //==================================================================================================

	//================================== Stemming Nazief-Adriani =======================================
	function cekKamus($jawab){ 
		$query = $this->ujian_siswa_model->get_by_id_stemming($jawab);

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
			}
		}
	}
?>