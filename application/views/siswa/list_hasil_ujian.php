  <!-- Content Header (Page header) -->
      <section class="content-header">
        <section class="content">
         <div class="login-box-body" style="text-align: center;">
          <img src="<?php echo base_url();?>assets/images/logo.png" class="user-image" alt="User Image" width="60">
          <h4>Aplikasi Ujian Essay SMK Negeri 1 Tambelang</h4>
        </div>
     
        <div class="box box-default">
          <div class="box-header with-border">
            <div class="col-md-8">
              <div  class="row">
                <div class="col-md-6">
                  <center><img src=<?php echo base_url();?><?php echo "assets/images/thumb/".$this->session->userdata('foto'); ?> class="user-image" alt="User Image" width="230" height="320"></center>
                </div>
                <div class="col-md-6">
                <h4>
                  <b>Keterangan Hasil Ujian Anda Adalah :</b>
                </h4>
                  <h4>
                    <table class="table" border="0">
                      <tr>
                        <td>Nama</td>
                        <td>: <?php echo $nama; ?></td>
                      </tr>
                      <tr>
                        <td>NIS</td>
                        <td>: <?php echo $nis; ?></td>
                      </tr>
                      <tr>
                        <td>Nilai</td>
                        <td>: <b><?php echo $nilai_ujian; ?></b></td>
                      </tr>
                      <tr>
                        <td><a href="<?php echo base_url(); ?>"><button class="btn btn-danger">Logout</button></a></td>
                        <td></td>
                      </tr>
                    </table>
                   </h4>
                   <br>
                </div>
              </div> 
            </div>
          </div>
        </div>
      </section>
      <!-- /.content -->