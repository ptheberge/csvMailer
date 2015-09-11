<?php

namespace Ccs\Controllers;

class Csv extends Controller
{
    protected function parseCsv($csv) {
        $csv_rows = array_map('str_getcsv', explode("\n", $csv));
        foreach($csv_rows as &$row) {
            foreach($row as &$cell) {
                $cell = utf8_encode($cell);
            }
        }
        return json_encode($csv_rows);
    }

    public function index() {
        echo 'index function if needed';
    }

    public function upload() {
        $file = $_FILES['fileToUpload']['tmp_name'];
        $csv = file_get_contents($file);
        
        $json = $this->parseCsv($csv);

        var_dump(json_decode($json));

    }
}