 <!-- SELECT2 EXAMPLE -->
      <div class="box box-default">
      
        <!-- /.box-header -->
        <div class="box-body">
          <div class="row">
            <div class="col-md-6">
               <form action="<?php echo base_url(); ?>soal_ujian/update_action" method="post" enctype="multipart/form-data">
                <div class="col-md-4">
                        <div style="margin-top: 4px"  id="message">
                            <?php echo $this->session->userdata('message') <> '' ? $this->session->userdata('message') : ''; ?>
                            
                        </div>
                </div>
                <br><br>
                
            <div class="form-group">
                <fieldset>
                    <label for="id_soal">Nomor Soal</label>
                    <input type="number"class="form-control" name="id_soal" value="<?php echo($id_soal) ?>" id="id_soal" placeholder="Nomor soal" readonly></input>
               </fieldset>
              </div>
             <div class="form-group">
                <fieldset>
                    <label for="pertanyaan">Pertanyaan Soal</label>
                    <textarea class="form-control" rows="3" name="pertanyaan" id="pertanyaan" placeholder="pertanyaan soal"><?php echo($pertanyaan) ?></textarea>
               </fieldset>
              </div>
             <div class="form-group">
                <fieldset>
                    <label for="pertanyaan">Kunci Jawaban</label>
                    <textarea class="form-control" rows="3" name="jawaban" id="jawaban" placeholder="kunci jawaban"><?php echo($jawaban) ?></textarea>
               </fieldset>
              </div>
             
              <br>
              <button type="submit" class="btn btn-primary btn-small">Simpan</button> 
              <a href="<?php echo site_url('soal_ujian') ?>" class="btn btn-danger btn-small">Keluar</a>
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
