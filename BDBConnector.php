<?php

/**
 * Created by PhpStorm.
 * User: shervin
 * Date: 10/23/15
 * Time: 3:24 PM
 */
class BDBConnector {
    private $filePath;

    public function __construct($path){
        if (!file_exists($path)) return false;
        $this->filePath = $path;
        return true;
    }

    public function createFile($filePath){
        $this->filePath = $filePath;
        if (file_exists($this->filePath)) return true;

        $db = dba_open($this->filePath, "n" ,"db4");
        dba_close($db);

        return true;
    }

    public function dbExists(){
        return ((file_exists($this->filePath)) ? true : false);
    }

    public function getAll(){
        $keys = $this->getKeys();
        $db = dba_open($this->filePath, 'wl', 'db4');
        $table_data = array();
        for ($i = 0; $i < count($keys); $i++) {
            $table_data[$keys[$i]] = dba_fetch($keys[$i], $db);
        }
        dba_close($db);
        return $table_data;
    }

    public function getKeys(){
        $db = dba_open($this->filePath, 'wl', 'db4');
        $key = dba_firstkey($db);
        $keys_array = array();
        while ($key != false) {
            if (true) {          // remember the key to perform some action later
                $keys_array[] = $key;
            }
            $key = dba_nextkey($db);
        }

        dba_close($db);
        return $keys_array;
    }

    public function __get($name){
        $db = dba_open($this->filePath, 'wl', 'db4');
        $value = ((dba_exists($name, $db)) ? dba_fetch($name, $db) : null);
        dba_close($db);
        return $value;
    }

    public function __set($name, $value){
        $db = dba_open($this->filePath, 'wl', 'db4');
        $response = dba_replace($name, $value, $db);
        return $response;
    }

    public function remove($key){
        $db = dba_open($this->filePath, 'wl', 'db4');
        $response = dba_delete($key, $db);
        return $response;
    }
}