 
 <!DOCTYPE html>
<html>
<head>
  <title>Home Siswa</title>
 <link rel="stylesheet" href="<?php echo base_url(); ?>assets/font-awesome/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/ionicons/css/ionicons.min.css">
  
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/bootstrap/css/bootstrap.min.css">
 
  <!-- Theme style -->
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/AdminLTE.min.css">
  <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/skins/_all-skins.min.css">
<script type="text/javascript">
 
</script>
 
</head>

<body class="hold-transition skin-blue layout-top-nav" >

<div class="wrapper">

  <header class="main-header">
    <nav class="navbar navbar-static-top">
      <div class="container">
        <div class="navbar-header">
          <b class="navbar-brand">Beranda </b>
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse">
            <i class="fa fa-bars"></i>
          </button>
        </div>

          <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse pull-left" id="navbar-collapse">
        <div id='timer'></div>
          <b class="navbar-brand" > <div id="pesan"></div></b>
        
        </div>
         <!-- Navbar Right Menu -->
      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
       
        
          <!-- User Account Menu -->
          <li class="dropdown user user-menu">
            <!-- Menu Toggle Button -->
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <!-- The user image in the navbar-->
              <img src=<?php echo base_url();?><?php echo "assets/images/thumb/".$this->session->userdata('foto'); ?> class="user-image" alt="User Image">
              <!-- hidden-xs hides the username on small devices so only the image appears. -->
              <span class="hidden-xs"><?php echo $this->session->userdata('username');?> </span>
            </a>
            <ul class="dropdown-menu">
              <!-- The user image in the menu -->
              <li class="user-header">
                <img src=<?php echo base_url();?><?php echo "assets/images/thumb/".$this->session->userdata('foto'); ?> class="img-circle" alt="User Image">

                <p>
                  <?php echo $this->session->userdata('username');?> - Siswa
                </p>
              </li>
            
              <!-- Menu Footer-->
              <li class="user-footer">
                <div class="pull-right">
                  <a href="<?php echo base_url(); ?>" class="btn btn-default btn-flat">Sign out</a>
                </div>
              </li>
            </ul>
          </li>
        
        </ul>
      </div>
        <!-- /.navbar-collapse -->
     
        <!-- /.navbar-custom-menu -->
      </div>
      <!-- /.container-fluid -->
    </nav>
  </header>
  <!-- Full Width Column -->
  <div class="content-wrapper">
  
    <div class="container">
    <!-- Content Header (Page header) -->
      <section class="content-header">
        <section class="content">
         <div class="login-box-body" style="text-align: center;">
          <img src="<?php echo base_url();?>assets/images/logo.png" class="user-image" alt="User Image" width="60">
          <h4>Aplikasi Ujian Essay SMK Negeri 1 Tambelang</h4>
        </div>
        <div class="box box-default">
        <div class="box-header with-border">
        <h4>
          Ujian Anda Telah Selesai, Silahkan Klik Tombol Selesai Dibawah Ini.
        </h4>
       <p>
       <br>
       <b>Jangan Lupa Klik Tombol Selesai Dibawah Ini !!</b>
       <br><br>
       <a href="<?php echo base_url(); ?>ujian_siswa/hitung_nilai"><button class="btn btn-primary">Selesai</button></a>
       </p>
       <br>
        
      </div>
        
      </div>
      </section>
      <!-- /.content -->
    </div>
  

    <!-- /.container -->
  </div>
  <!-- /.content-wrapper -->
  <footer class="main-footer">
    <div class="container">
      <strong>&copy;</strong> 2018 <b>Dina Mariana</b>
    </div>
    <!-- /.container -->
  </footer>
</div>
<!-- ./wrapper -->

<!-- jQuery 2.2.0 -->
<script src="<?php echo base_url(); ?>assets/plugins/jQuery/jQuery-2.2.0.min.js"></script>
<!-- Bootstrap 3.3.6 -->
<script src="<?php echo base_url(); ?>assets/bootstrap/js/bootstrap.min.js"></script>
<!-- SlimScroll -->
<script src="<?php echo base_url(); ?>assets/plugins/slimScroll/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script src="<?php echo base_url(); ?>assets/plugins/fastclick/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="<?php echo base_url(); ?>assets/dist/js/app.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="<?php echo base_url(); ?>assets/dist/js/demo.js"></script>
</body>
</html>
 