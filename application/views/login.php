<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Login Pengguna</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.6 -->
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/bootstrap/css/bootstrap.min.css">

  <!-- Theme style -->
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/AdminLTE.min.css">
  <!-- iCheck -->
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/iCheck/square/blue.css">


</head>
<body class="hold-transition login-page">
<div class="login-box">
  
  <!-- /.login-logo -->

  <div class="login-box-body" style="text-align: center;">
    <img src="<?php echo base_url();?>assets/images/logo.png" class="user-image" alt="User Image" width="60">
  <div class="login-logo">
    <b>Login </b>Pengguna
  </div>
    <h5>Aplikasi Ujian Essay SMK Negeri 1 Tambelang</h5>
    <form action="<?php echo base_url(); ?>login/getlogin" method="post">
      <div class="form-group">
        <input type="name" class="form-control" placeholder="Username" name="username" required>
        
      </div>
      <div class="form-group">
        <input type="password" class="form-control" placeholder="Password" name="password" required>
                               <?php
                                $info = $this->session->flashdata('info');
                                if(!empty($info))
                                {
                                  echo $info;
                                }
                                ?>
      </div>
      <div class="row">
       
        <!-- /.col -->
        <div class="col-xs-12">
          <button type="submit" class="btn btn-primary">Login</button>
        </div>
        <!-- /.col -->
      </div>
    </form>

   
    <!-- /.social-auth-links -->

  </div>
  <!-- /.login-box-body -->
</div>
<!-- /.login-box -->

<!-- jQuery 2.2.0 -->
<script src="<?php echo base_url(); ?>assets/plugins/jQuery/jQuery-2.2.0.min.js"></script>
<!-- Bootstrap 3.3.6 -->
<script src="<?php echo base_url(); ?>assets/bootstrap/js/bootstrap.min.js"></script>
<!-- iCheck -->
<script src="<?php echo base_url(); ?>assets/plugins/iCheck/icheck.min.js"></script>
</body>
</html>
