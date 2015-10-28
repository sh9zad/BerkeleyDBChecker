<?php
/**
 * Created by PhpStorm.
 * User: shervin
 * Date: 10/23/15
 * Time: 2:49 PM
 */

require_once __DIR__ . '/BDBConnector.php';

$base_url = '';
$file = '';
$upload_path = __DIR__ . '/uploads';
$action = '';
$data = array();
$files_list = array();
getFiles();
if (!empty($_POST['action'])){
    $action = $_POST['action'];
    switch ($action){
        case 'load_db_file':
            $base_url = $_POST['base_path'];
            $file = $_POST['file_name'];
            $data = loadTable($_POST['base_path'], $_POST['file_name']);
            break;
        case 'upload_file':
            $file = basename($_FILES["fileToUpload"]["name"]);
            $base_url = $upload_path;
            $ext = end((explode(".", $file)));
            if ($ext != 'db'){
                $action = '';
                echo "<h2>Not a valid DB file. Select a *.db file</h2>";
            }
            $target_file = $upload_path . '/' .$file;
            if(isset($_POST["submit"])) {
                if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
                    $data = loadTable($upload_path, basename($_FILES["fileToUpload"]["name"]));
                    $action = 'load_db_file';
                } else {
                    $action = '';
                    echo "Sorry, there was an error uploading your file.<br>";
                    print_r($_FILES);
                }
            }
            break;
    }
}

function loadTable($base, $file){
    $db_admin = new BDBConnector($base . '/' . $file);
    if ($db_admin == false) return false;
    return $db_admin->getAll();
}

function getFiles(){
    global $upload_path;
    global $files_list;
    if ($handle = opendir($upload_path)) {
        while (false !== ($entry = readdir($handle))) {
            if ($entry != "." && $entry != "..") {
                $files_list[] = $entry;
            }
        }

        closedir($handle);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap-theme.min.css">

    <!-- Vendor: Javascripts -->
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
    
    <style>
        .contain {
            width: 90%;
            margin-left: 5%;
            margin-top: 2%;
        }
        .download_box {
            color: darkgreen;
            font-size: 18px;
            text-align: center;
        }
        .delete_box {
            color: darkred;
            font-size: 18px;
            text-align: center;
        }
    </style>
    <title>Berkely DB Checker</title>
</head>
<body>
<div class="contain">
    <h2>BDB Editor</h2>
    <div class="row row-fluid">
        <div class="col-lg-7">
            <div class="row">
                <div class="col-lg-12">
                    <h3>Select Your File to Upload</h3>
                    <form class="form-inline" action="index.php" method="post" enctype="multipart/form-data">
                        <input id="action" name="action" type="hidden" value="upload_file">
                        <div class="form-group">
                            <label for="fileToUpload">Select the DB file:</label>
                            <input id="fileToUpload" name="fileToUpload" type="file" accept=".db" class="form-control">
                            <input type="submit" value="Upload DB File" name="submit" class="btn btn-primary">
                        </div>
                    </form>
                </div>
            </div>
            <br>
            <div class="row row-fluid">
                <div class="col-lg-12">
                    <form id="frm_load_file" method="post" action="#"  role="form" class="form-inline">
                        <input type="hidden" name="action" id="action" value="load_db_file">
                        <div class="form-group">
                            <label for="base_path">Base Path: </label>
                            <input type="text" class="form-control" id="base_path" name="base_path" value="<?= $base_url ?>">
                        </div>
                        <div class="form-group">
                            <label for="file_name">File name: </label>
                            <input type="text" class="form-control" id="file_name" name="file_name" value="<?= $file ?>">
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-success">Load</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <h3>List of Files</h3>
            <table class="table table-bordered">
                <thead><tr><th class="text-center">#</th><th>File Name</th><th class="text-center">Download</th><th class="text-center">Delete</th></tr></thead>
                <tbody>
                <?php foreach ($files_list as $key => $value) { ?>
                    <tr>
                        <td class="text-center"><?= $key ?></td>
                        <td><?= $value ?></td>
                        <td class="download_box"><a href="#" class="download_box download_file"><span data-filename="<?= $value ?>" class="glyphicon glyphicon-download"></span></a></td>
                        <td class="delete_box"><a href="#" class="delete_box delete_file"><span data-filename="<?= $value ?>" class="glyphicon glyphicon-remove-circle"></span></a></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
<br>
    <?php if ($action == 'load_db_file') {?>
    <div class="row row-fluid">
        <div class="col-lg-12">
            <h3>File Content</h3>
            <table id="tbl_data" class="table table-bordered table-hover">
                <thead><tr><th>#</th><th>Key</th><th>Value</th><th>Edit</th><th>Delete</th></tr></thead>
                <tbody>
                <?php $count = 1; foreach ($data as $key => $value) { ?>
                    <tr>
                        <td><?= $count ?></td>
                        <td><?= $key ?></td>
                        <td><?= $value ?></td>
                        <td class="edit_row"><button id="btn_edit" type="button" class="btn btn-warning">Edit</button></td>
                        <td><button type="button" class="btn btn-danger" onclick="delete_line('<?= $key ?>');">Delete</button></td>
                    </tr>
                <?php $count++; } ?>
                </tbody>
            </table>
            <input type="button" id="btn_add" class="btn btn-info" value="Add">
        </div>
    </div>
    <?php } ?>
</div>
<iframe id="download_frame" class="hidden"></iframe>
</body>
<script type="text/javascript">
    var file = $('#base_path').val() + '/' + $('#file_name').val();
    var url = 'ajax_handler.php';

    $('#btn_add').click(function() {
        addTableRow($('#tbl_data'));
    });
    $('#tbl_data').on('click','.edit_row',function() {
        var text = $('#btn_edit').text();
        console.log(text);
        if(text == 'Save'){
            var data = [];
            $(".txt_edit").each(function() {
                console.log($(this).val());
                data.push($(this).val());
            });
            $.ajax({
                url: url,
                type: 'post',
                data: {action: 'edit_data', data: JSON.stringify(data), file: file},
                success: function(data){
                    console.log(data);
                    location.reload();
                },
                error: function (data) {
                    console.log(data);
                }
            });

        }
        $('#btn_edit').text(((text == 'Save') ? 'Edit' : 'Save'));

        $(this).siblings().each(
            function(){
                if ($(this).find('input').length){
                    $(this).text($(this).find('input').val());
                }
                else {
                    var t = $(this).text();
                    if(t != 'Delete')
                        $(this).html($('<input />',{'value' : t, 'class' : 'txt_edit'}).val(t));
                }
            });
    });
    $('.download_file').on('click', function(e){
        $.ajax({
            url: url,
            type: 'post',
            data: {action: 'download_file', filename: e.target.dataset.filename},
            success: function(data){
                console.log(data);
                $("#download_frame").attr('src', data);
            },
            error: function (data) {
                console.log('error', data);
            }
        });
//        console.log(e.target.dataset.filename);
    });
    $('.delete_file').on('click', function(e){
        if(confirm("Are you sure?")){
            $.ajax({
                url: url,
                type: 'post',
                data: {action: 'delete_file', filename: e.target.dataset.filename},
                success: function(data){
                    console.log(data);
                    location.reload();
                },
                error: function(data){
                    console.log(data);
                }
            });
        }
    });

    function delete_line(key){
        if(confirm('Are you sure?'))
            $.ajax({
                url: '../handler/ajax_handler_db_check.php',
                type: 'post',
                data: {action: 'remove_data', key: key, file: file},
                success: function(data){
                    console.log(data);
                    location.reload();
                },
                error: function (data) {
                    console.log(data);
                }
            });
    }
    function addTableRow(jQtable) {
        var row = $('<tr />');

        for (var i = 0; i <= 2; i++) {
            var cell = $('<td />');
            var input = $('<input type="text" name="new_row" class="new_row" />');
            cell.append(input);
            row.append(cell);
        }

        row.append('<td ><button id="btn_new_entry" type="button" class="btn btn-primary">Save</button></td>');
        row.append('<td><button disabled type="button" class="btn btn-danger">Delete</button></td>');
        jQtable.append(row);

        $('#btn_new_entry').on('click', function(){
            var data = [];
            $(".new_row").each(function() {
                console.log($(this).val());
                data.push($(this).val());
            });

            $.ajax({
                url: url,
                type: 'post',
                data: {action: 'new_entry', data: JSON.stringify(data), file: file},
                success: function(data){
                    console.log(data);
                    location.reload();
                },
                error: function (data) {
                    console.log(data);
                }
            });
        });
    }
</script>
</html>
