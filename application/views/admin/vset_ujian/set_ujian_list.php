
      <div class="box box-default">
      
        <!-- /.box-header -->
        <div class="box-body">

          <div class="row">
            <div class="col-md-6">
              <!-- SELECT2 EXAMPLE -->
            <h4>Jumlah pertanyaan &nbsp;:&nbsp;&nbsp; <b><?php echo $total_soal ?> </b> Soal</h4>

            <?php
                $start = 0;
                foreach ($set_ujian_data as $row)
                {
              ?>
              <h4>Lama pengerjaan &nbsp;:&nbsp;&nbsp; <b> &nbsp;&nbsp;
              <?php echo $row->waktu_ujian_menit ?></b> Menit</h4>

              <?php 
                }
               ?>

            <br>
            <form action="set_ujian/update_action" method="post">
              
              <div class="form-group">

                    <label >Ubah Waktu <?php echo form_error('waktu') ?></label>
                    <input type="number"  class="form-control" name="waktu" id="waktu" placeholder="dalam menit" required />
              </div>
               
           
              <button type="submit" class="btn btn-primary btn-small">Simpan</button> 
             
              <br>
              <br>
          </form>
             
              <!-- /.form-group -->
            </div>
           
          </div>
          <!-- /.row -->
        </div>
      
      </div>
      <!-- /.box -->
