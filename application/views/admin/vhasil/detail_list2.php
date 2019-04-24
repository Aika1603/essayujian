 <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <br>
           
            <!-- /.box-header -->
            <div class="box-body">

              <table id="table" class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th>Soal</th>
                    <th>s1</th>
                    <th>s2</th>
                    <th>m</th>
                    <th>dj = 1/3(m/s1+m/s2+m-t/m)</th>
                    <th>dw = dj+(lp(1-dj))</th>
                    <th>NA = dw*100</th>
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
                    <td><?php echo $row->string_satu ?></td>
                    <td><?php echo $row->string_dua ?></td>
                    <td><?php echo $row->m ?></td>
                    <td><?php echo $row->nilai_jaro ?></td> 
                    <td><?php echo $row->nilai_winkler ?></td> 
                    <td><?php echo $row->na ?></td>
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
