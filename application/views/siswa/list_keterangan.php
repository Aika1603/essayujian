  <!-- Content Header (Page header) -->
      <section class="content-header">
        <section class="content">
        <div class="login-box-body" style="text-align: center;">
          <img src="<?php echo base_url();?>assets/images/logo.png" class="user-image" alt="User Image" width="60">
          <h4>Aplikasi Ujian Essay SMK Negeri 1 Tambelang</h4>
        </div>
     
        <div class="box box-default">
          <div class="box-header with-border">
       <p>
       <br>
       <h4><b>Ikutilah Petunjuk Dibawah Ini!!!</b></h4><br>
       <b>Bacalah terlebih dahulu keterangan dibawah ini sebelum anda memulai ujian.</b><br>
       1.  Lama waktu pengerjaan soal yang diberikan adalah <b><?php echo $total_menit ?> </b>menit untuk setiap soal ujian.<br>
       2.  Soal ujian berjumlah <b><?php echo $total_soal ?></b> soal.<br>
       3.  Isilah pada kolom jawaban yang telah tersedia.<br> 
       4.  Isilah semua soal dengan bahasa yang baik dan benar.<br>
       5.  Soal tidak bisa diulangi ketika Anda sudah mengklik tombol <b>Kirim Jawaban</b> tetapi akan masuk ke soal selanjutnya.<br>
       5.  Sistem akan berhenti otomatis apabila waktu yang diberikan telah habis.<br>
       <br><b>Selamat Mengerjakan...</b><br>
       </p>
       <br>
        <a href="<?php echo site_url('ujian_siswa') ?>" class="btn btn-info">Mulai Ujian</a>
      </div>
        
      </div>
      </section>
      <!-- /.content -->