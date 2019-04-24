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

                    <label for="varchar">Username <?php echo form_error('name') ?></label>
                    <input type="text"  class="form-control" name="name" id="name" placeholder="Username" value="<?php echo $name; ?>" />
              </div>
                 <div class="form-group">
                    <label for="varchar">Password <?php echo form_error('pwd') ?></label>
                    <input type="password" class="form-control" name="pwd" id="pwd" placeholder="Password" value="<?php echo $pwd; ?>" />
                </div>
              <div class="form-group">
                    <label for="varchar">Email <?php echo form_error('email') ?></label>
                    <input type="text" class="form-control" name="email" id="email" placeholder="Email" value="<?php echo $email; ?>" />
              </div>
             
              <div class="form-group">
                    <label for="foto">Foto <?php echo form_error('foto') ?> </label>
                    <input type="file" class="form-control" name="foto"  id="foto"  value="<?php echo $foto; ?>" />
              </div>
                <br>
              <input type="hidden" name="id_admin" value="<?php echo $id_admin; ?>" /> 
              <button type="submit" class="btn btn-primary btn-small">Simpan</button> 
              <a href="<?php echo site_url('admin') ?>" class="btn btn-danger btn-small">Keluar</a>
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
