<script src="<?php echo base_url(); ?>assets/dist/js/jquery.min.js"></script>
 <?php
  if ($query1->num_rows()>0){
    foreach ($query1->result() as $row) {?>
     <script>
          var url = "http://localhost/ujianesai/ujian_siswa/hitung_nilai/";    
          var detik = 60; // dalam detik
          var menit = <?php echo $row->menit.' '; ?> // dalam detik
          function countDown() {
              if (detik > 0) {
                  detik--;
                  
              } else{
                   menit--;  
                   detik = detik + 60;
              }
              
              if (menit==0) {
                  detik=0;
                  window.location.href = url;
              };
              $('#pesan').html('> Waktu Ujian : ' + menit +' menit | '+ detik + ' detik');
              
              setTimeout("countDown()", 1000);
            // beri nilai detik
              var waktu_menit=menit;
              var waktu_detik=detik;
              document.form1.waktu_menit.value=waktu_menit;
              document.form1.waktu_detik.value=waktu_detik;
          }  
          countDown(); 
      </script>  
      <?php
    }
  }
?>     
    <div class="container">
      <!-- Main content -->
      <section class="content">
         <div class="login-box-body" style="text-align: center;">
          <img src="<?php echo base_url();?>assets/images/logo.png" class="user-image" alt="User Image" width="60">
          <h4>Aplikasi Ujian Essay SMK Negeri 1 Tambelang</h4>
        </div>
        <div class="box box-default">
            <div class="box-header with-border">
            <?php
            if ($query2->num_rows()>0){
            foreach ($query2->result() as $row) 
            {
                $id = $row->id_soal;
            ?>

             <form action="<?php echo base_url(); ?>ujian_siswa/proses" method="post" role="form" >
                <p><?php echo $row->pertanyaan; ?></p>
                 <!-- textarea -->
                <div class="form-group">
                  <label>Jawaban :</label>
                  <textarea class="form-control" name="isi_jawaban[<?php echo $id; ?>]" rows="3" placeholder="Isi..." required></textarea>
                </div>
                <input type="hidden" id="waktu_menit" name="waktu_menit" >
                <input type="hidden" id="id_soal" name="id_soal[<?php echo $id; ?>]" value="<?php echo $row->id_soal.' '; ?>">

                 <?php
                }
              }
              ?>
              <button type="submit" class="btn btn-info">Kirim Jawaban</button>
             </form>
          </div>
          </div>
        <!-- /.box -->
      </section>
    </div>