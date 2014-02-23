<?php //uploader single gallery
include('sample_header.php'); 

$attachmentID = get_query_var('photo-single');
$attachmentData = query_user_uploads('single', $attachmentID);
$displayImage = wp_get_attachment_image($attachmentID, 'full');

$buttonsBuild = locate_buttons($attachmentID, query_user_uploads('all'));

$prev = $buttonsBuild['prev']->ID;
$next = $buttonsBuild['next']->ID;

?>


<div id="carousel-example-generic" class="member carousel slide" data-ride="carousel">
<div class="member-photo">
    <?php echo $displayImage; ?>
                <div class="image-stats-container">
                    <ul class="imageStats">
                        <li>
                            <span class="mini-progress">
                                <?php echo $attachmentData['date_taken']; ?>
                            </span>
                        </li>
                        <li>
                            <button type="button" class="btn btn-xs btn-danger pull-right">Delete</button>
                        </li>
                    </ul>
                </div>
</div>

  <a class="left carousel-control" href="../<?php echo $prev; ?>/" data-slide="prev">
    <span class="glyphicon glyphicon-chevron-left"></span>
  </a>

  <a class="right carousel-control" href="../<?php echo $next; ?>/" data-slide="next">
    <span class="glyphicon glyphicon-chevron-right"></span>
  </a>

</div>


<h1><?php echo $attachmentData['user_title']; ?></h1>

