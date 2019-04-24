 <div class="row">
        <div class="col-xs-12">
          <div class="box">
          <div class="col-md-4">
                <div style="margin-top: 4px"  id="message">
                    <?php echo $this->session->userdata('message') <> '' ? $this->session->userdata('message') : ''; ?>
                </div>
            </div>
            <br>
           <!--  <div class="box-header">
             <a href="<?php echo site_url('hasil_tes/cetak') ?>" class="btn btn-primary">Cetak Data</a>
            </div> -->

            <!-- /.box-header -->
            <div class="box-body">

              <table id="table" class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th>No</th>
                    <th>NIS</th>
                    <th>Nama</th>
                    <th>Nilai Akhir</th>
                    <th>Lihat Detail</th>
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
                    <td><?php echo $row->nis?></td>
                    <td><?php echo $row->nama ?></td>
                    <td><?php echo $row->nilai_akhir ?></td>
                    
                    <td><a href="<?php echo base_url(); ?>hasil_tes/lihat_detail/<?php echo $row->nis ?>" class="btn btn-info btn-sm" ><i class="fa fa-eye "></i>  </a></td>
                   
                    <td width="50px">
                      <center>
                           <a class="btn btn-danger btn-sm" href="<?php echo base_url(); ?>hasil_tes/delete/<?php echo $row->nis?>" onclick="return confirm('Hapus')" data-toggle="tooltip" data-placement="top" title="HAPUS">
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
            </div>
            <!-- /.box-body -->
          </div>
          <!-- page script -->
</div>
</div>
<br>
<br>
