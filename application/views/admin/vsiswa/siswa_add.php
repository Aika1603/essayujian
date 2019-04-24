 <!-- SELECT2 EXAMPLE -->
      <div class="box box-default">
      
        <!-- /.box-header -->
        <div class="box-body">
          <div class="row">
            <div class="col-md-6">
               <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data">
                <div class="col-md-4">
                        <div style="margin-top: 4px"  id="message">
                            <?php echo $this->session->userdata('message') <> '' ? $this->session->userdata('message') : ''; ?>
                            
                        </div>
                </div>
                <br><br>

              <div class="form-group">
                    <label>Nis <?php echo form_error('nis') ?></label>
                    <input type="text"  class="form-control" name="nis" id="nis" placeholder="NIS" />
              </div>
                 <div class="form-group">
                    <label>Nama <?php echo form_error('nama') ?></label>
                    <input type="text" class="form-control" name="nama" id="nama" placeholder="Nama"  />
                </div>
             <div class="form-group">
                    <label for="foto">Foto <?php echo form_error('foto') ?> </label>
                    <input type="file" class="form-control" name="foto"  id="foto"   />
              </div>
              <div class="form-group">
                    <label >Log Password<?php echo form_error('password') ?></label>
                    <input type="password" class="form-control" name="password" id="password" placeholder="Password"  />
              </div>
                <br>
              <button type="submit" class="btn btn-primary btn-small">Simpan</button> 
              <a href="<?php echo site_url('siswa') ?>" class="btn btn-danger btn-small">Keluar</a>
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
