<?php //gallery
include('sample_header.php'); 


$pictureCount = query_user_uploads('count');
$allImages = (array)query_user_uploads('all');

$pg = (get_query_var('pg')) ? get_query_var('pg') : 1;
$prev = $pg -1;
$next = $pg +1;
?>


        <div class='row'>
            <div clas="col-xs-12">
                <h1><?php echo $pictureCount; ?> Pictures Uploaded</h1>
            </div>
            <div class='col-xs-6 col-md-3'>
<?php 
$getPics = photo_gallery_paginated(8);
$thumbs = $getPics['array'];
$max_num_pages = $getPics['max_num_pages'];
$count=0;
$i=1;

foreach($thumbs as $image) {
    if($count%1 == 0 && $count != 0) { 
    echo '</div>';
    echo '<div class="col-xs-6 col-md-3">';
    }
    echo '<a class="thumbnail view-photo" href="../photo-single/'.$image->attachment_id.'/"><img src="'.wp_get_attachment_thumb_url( $image->attachment_id  ).'"></a>';
    $count++;
    $i++;

}  
?>
 </div> 
</div> 
      
<ul class="pagination">
<li class="<?php if ($pg == 1) echo 'disabled'; ?>"><a href="?pg=<?php echo $prev; ?>">Prev </a></li>
<?php 
for ($i = 1; $i<=$max_num_pages; $i++) { 
    $paginationResult = "<li><a href='?pg=".$i."'>".$i."</a> ";
    if ($i == $pg) {
    $paginationResult = "<li class='active'><a href='?pg=".$i."'>".$i."</a> "; 
    } else {
    $paginationResult = "<li><a href='?pg=".$i."'>".$i."</a> "; 
    }
    echo $paginationResult;
}; ?>
<li class="<?php if ($pg == $max_num_pages) echo 'disabled'; ?>"><a href="?pg=<?php echo $next; ?>">Next</a></li>
</ul>
