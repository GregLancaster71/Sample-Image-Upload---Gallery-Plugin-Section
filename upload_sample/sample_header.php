<?php //sample header
global $_uploads_by; 
$gallery = get_query_var('photo-gallery');
$single = get_query_var('photo-single');
$upload = get_query_var('uploadsby');

$tab1 = null;
$tab2 = null;

if ($gallery || $single)
    $tab2 = 'active';
else
    $tab1 = 'active';
?>

<nav class="navbar navbar-default" role="navigation">
      <!-- We use the fluid option here to avoid overriding the fixed width of a normal container within the narrow content columns. -->
      <div class="container-fluid">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-7">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
        </div>

        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-7">
          <ul class="nav navbar-nav">
            <li class="<?php echo $tab1; ?>"><a href="/uploadsby/<?php echo $_uploads_by->user_login; ?>/">Image Upload</a></li>
            <li class="<?php echo $tab2; ?>"><a href="/uploadsby/<?php echo $_uploads_by->user_login; ?>/photo-gallery/">Image Gallery</a></li>
          </ul>
        </div><!-- /.navbar-collapse -->
      </div>
    </nav>