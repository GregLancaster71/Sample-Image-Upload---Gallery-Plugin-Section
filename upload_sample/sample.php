<?php 
include('sample_header.php');

global $post;

if (isset($_POST['submit'])) {
    
$fields = array(
'Image Title' => $_POST['user_title'],
'Image Caption' => $_POST['user_desc'],
'Image Date' => $_POST['date_taken']
);

$error = null;

foreach ($fields as $key => $value) {
     if(!isset($value) || trim($value) == '') {
        $error = 1;
        $message = '<div class="alert alert-danger">You did not enter an '.$key.'</div>';
        echo $message;
     }
}
    
if ($_FILES['upload_attachment']['error']) {
    $error = 1;
    $message = '<div class="alert alert-danger">You did not select a file! </div>';
    echo $message;
}

    
    
if (!isset($error)) {
    
    
$imageMetaData = array(
'user_upload' => true,
'user_title' => $_POST['user_title'],
'user_desc' => $_POST['user_desc'],
'date_taken' => $_POST['date_taken']
);
    
if ( $_FILES ) {
    
	$files = $_FILES['upload_attachment'];
	foreach ($files['name'] as $key => $value) {
		if ($files['name'][$key]) {
			$file = array(
				'name'     => $files['name'][$key],
				'type'     => $files['type'][$key],
				'tmp_name' => $files['tmp_name'][$key],
				'error'    => $files['error'][$key],
				'size'     => $files['size'][$key]
			);
 
			$_FILES = array("upload_attachment" => $file);
 
			foreach ($_FILES as $file => $array) {
                $post_id = '';
				$newupload = insert_gallery_attachment($file,$post_id );
                //individual_image_upload($newupload);
                
			}
            
		}
	}
}

foreach( (array)$imageMetaData as $key => $value ) {
update_post_meta($newupload, $key, $value);
}   

    echo '<div class="alert alert-success"><a href="photo-single/'.$newupload.'/">Your Image</a> Successfully Added To The <a href="photo-gallery/">Gallery</a> </div>';
}
}
?>

<form role="form" action="#" method="post" enctype="multipart/form-data">
    <div class="row">
        <div class="col-md-6">
            <div class="fileinput fileinput-new" data-provides="fileinput">
                <div class="fileinput-new thumbnail" style="min-width: 300px; min-height: 250px;">
                    <img data-src="holder.js/300x250" alt="...">
                </div>
                <div class="fileinput-preview fileinput-exists thumbnail" style="min-width: 300px; min-height: 250px;"></div>
                <div>
                    <span class="btn btn-default btn-file">
                        <span class="fileinput-new">Select Image</span>
                        <span class="fileinput-exists">Change</span>
                        <input type="file" id="upload_attachment[]" name="upload_attachment[]">
                    </span>
                    <a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput">Remove</a>
                </div>
            </div>

        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="user_title">Image Title</label>
                <input type="text" class="form-control" id="user_title" name="user_title" placeholder="Enter a Title">
            </div>
            <div class="form-group">
                <label for="user_title">Image Caption</label>
                <textarea class="form-control" name="user_desc" id="user_desc" rows="3"></textarea>
            </div>
            <div class="form-group">
                <label for="date_taken">Image Title</label>
                <input type="date" class="form-control" name="date_taken" id="date_taken">
            </div>


            <input type="submit" name="submit" value="Upload" />
        </div>
    </div>
</form>