 <div class="row">
        <div class="col-xs-12">
          <div class="box">
          <div class="col-md-4">
                <div style="margin-top: 4px"  id="message">
                    <?php echo $this->session->userdata('message') <> '' ? $this->session->userdata('message') : ''; ?>
                </div>
            </div>
            <br>
            <div class="box-header">
            </div>

            <!-- /.box-header -->
            <div class="box-body">

              <table id="table" class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Foto</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $start = 0;
                foreach ($admin_data as $row)
                {
                    ?>
                <tr>
                    <td><?php echo ++$start ?></td>
                    <td><?php echo $row->nama ?></td>
                    <td><?php echo $row->email ?></td>
                    <td> <img src="<?=base_url()?>assets/images/thumb/<?php echo $row->foto ?> "width="30" height="30"> </td>
                    <td width="90px">
                      <center>
                          <a class="btn btn-info btn-sm" href="<?php echo base_url(); ?>admin/update/<?php echo $row->id_admin?>" data-toggle="tooltip" data-placement="top" title="DETAIL">
                          <i class="fa fa-edit "></i>  
                          </a>
                          <a class="btn btn-danger btn-sm" href="<?php echo base_url(); ?><?php echo $row->id_admin?>" onclick="return confirm('Hapus')" data-toggle="tooltip" data-placement="top" title="HAPUS">
                          <i class="fa fa-trash-o "></i> 
                          </a>
                      </center>
                   </td>
                </tr>
               <?php 
                }
                ?>
                </tbody>
              </table>
              <a href="<?php echo site_url('admin/create') ?>" class="btn btn-primary">Tambah Data</a>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- page script -->
</div>
</div>
<br>
<br>
