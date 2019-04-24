 <div class="row">
        <div class="col-xs-12">
          <div class="box">
          <div class="col-md-4">
                <div style="margin-top: 4px"  id="message">
                    <?php echo $this->session->userdata('message') <> '' ? $this->session->userdata('message') : ''; ?>
                    <?php echo $this->session->userdata('message2') <> '' ? $this->session->userdata('message2') : ''; ?>
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
                    <th>Pertanyaan</th>
                    <th>Kunci Jawaban</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $start = 0;
                foreach ($soal_ujian_data as $row)
                {
                    ?>
                <tr> 
                    <td ><?php echo $row->id_soal ?></td>
                    <td ><?php echo $row->pertanyaan ?></td>
                    <td ><?php echo $row->kunci_jawaban ?></td>
                    <td width="200px">
                      <center>
                          <a class="btn btn-info btn-sm" href="<?php echo base_url(); ?>soal_ujian/update/<?php echo $row->id_soal?>" data-toggle="tooltip" data-placement="top" title="DETAIL">
                          <i class="fa fa-edit "></i>  
                          </a>
                          <a class="btn btn-danger btn-sm" href="<?php echo base_url(); ?>soal_ujian/update/<?php echo $row->id_soal?>" onclick="return confirm('Hapus')" data-toggle="tooltip" data-placement="top" title="HAPUS">
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
              <a href="<?php echo site_url('soal_ujian/create') ?>" class="btn btn-primary">Tambah Data</a>

              <a href="<?php echo site_url('soal_ujian/proses') ?>" class="btn btn-warning">Proses Data</a>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- page script -->
</div>
</div>
<br>
<br>
