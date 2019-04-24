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
                    <th>ID Soal</th>
                    <th>Soal</th>
                    <th>Jawaban</th>
                    <th>Nilai Cosine</th>
                    <!-- <th>Kunci jawaban</th> -->
                </tr>
                </thead>
                <tbody>

                <?php
                $start = 0;
                foreach ($detail_data as $row)
                {
                    ?>
                <tr>
                    <td><?php echo $row->id_soal ?></td>
                    <td><?php echo $row->pertanyaan ?></td>
                    <td><?php echo $row->kunci_jawaban?></td>
                    <td><?php echo $row->nilai_cosine?></td>
                    <!-- <td><?php echo $row->kunci_jawaban ?></td> -->
                </tr>
               <?php 
                }
                    ?>

                </tbody>
              </table>
                <a href="<?php echo site_url('hasil_tes') ?>" class="btn btn-danger btn-small">Kembali</a>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- page script -->
</div>
</div>
<br>
<br>
