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
                    <th>Soal</th>
                    <!-- <th>Jawaban</th>
                    <th>Hasil praproses</th> -->
                    <th>Kunci jawaban</th>
                </tr>
                </thead>
                <tbody>

                <?php
                $start = 0;
                foreach ($detail_data as $row)
                {
                    ?>
                <tr>
                    <td><?php echo ++$start ?></td>
                    <!-- <td><?php echo $row->jawaban?></td>
                    <td><?php echo $row->hasil_stemming?></td> -->
                    <td><?php echo $row->kunci_jawaban ?></td>
                </tr>
               <?php 
                }
                    ?>

                </tbody>
              </table>
                <a href="<?php echo site_url(); ?>hasil_tes/lihat_detail/<?php echo $row->nis ?>" class="btn btn-danger btn-small">Kembali</a>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- page script -->
</div>
</div>
<br>
<br>
