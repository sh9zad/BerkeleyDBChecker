<?php
/**
 * Created by PhpStorm.
 * User: shervin
 * Date: 10/23/15
 * Time: 4:53 PM
 */
require_once __DIR__ . '/BDBConnector.php';
$upload_path = 'uploads';
$action = $_POST['action'];

if ($action == 'edit_data'){
    $data = json_decode($_POST['data'], true);

    $key = $data[1];
    $bdb_connector = new BDBConnector($_POST['file']);
    echo $bdb_connector->$key = $data[2];
}
elseif ($action == 'remove_data'){
    $bdb_connector = new BDBConnector($_POST['file']);
    echo $bdb_connector->remove($_POST['key']);
}
elseif ($action == 'new_entry'){
    $data = json_decode($_POST['data'], true);

    $key = $data[1];
    $bdb_connector = new BDBConnector($_POST['file']);
    echo $bdb_connector->$key = $data[2];
}
elseif ($action == 'download_file'){
    echo $upload_path . '/' . $_POST['filename'];
}
elseif ($action == 'delete_file'){
    unlink($upload_path . '/' . $_POST['filename']);
}
//echo json_encode($_POST['data']);