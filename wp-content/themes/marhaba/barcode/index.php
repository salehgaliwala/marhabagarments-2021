<?php require ('../wp-blog-header.php'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>How to generate barcode using PHP | Mitrajit's Tech Blog</title>
<link rel="stylesheet" href="bootstrap/css/bootstrap.min.css" />
<!--<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>-->

<script type="text/javascript" src="bootstrap/js/jquery.min.js"></script>
<script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>
<style>
.mtb-margin-top { margin-top: 20px; }
.top-margin { border-bottom:2px solid #ccc; margin:20px 0; display:block; font-size:1.3rem; line-height:1.7rem;}
.btn-success {
	cursor:pointer;
}
img.barcode {
    border: 1px solid #ccc;
    padding: 20px 10px;
    border-radius: 5px;
}
label {
    margin-bottom: 0rem;
    font-weight: bold;
    font-size: 13px;
}
.form-control {
    padding:0.2rem .75rem;
    font-size: 14px;
}
select.form-control:not([size]):not([multiple]) {
    height: auto;
}
#string, #size {
    height: 30px;
}
.btn {
    margin-bottom:30px;
}
</style>
</head>

<body>
	


    <div class="container-fluid">
        <div class="row">
            <div class="col-md-4"></div>
            <div class="col-md-4 text-center">
                <?php
                    $postid = $_REQUEST['postid'];
                    $ids = get_post_meta($postid, 'barcode_batch', true);
                    $ids = explode(",",$ids);
                    update_post_meta($postid,'print_status','true');
                    foreach($ids as $id)
                    {
                        $string = $id;
                        $type='code128';
                        $orientation='horizontal';
                        $size='20';
                        $print='true';

                    if($string != '') {
                        echo '</br>';
                        echo '</br>';
                        echo '<img class="barcode" alt="'.$string.'" src="barcode.php?text='.$string.'&codetype='.$type.'&orientation='.$orientation.'&size='.$size.'&print='.$print.'"/>';
                        echo '</br>';                      
                        echo '</br>';
                    
                    }

                }
                    
                ?>
            </div>
            <div class="col-md-4"></div>
        </div>
    </div>
	
</body>
</html>
