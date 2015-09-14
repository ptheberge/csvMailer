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

    protected function parseShortCodes($entity, $headers, $json) {

        $row_data = $json[$entity->similar[0]];

        foreach($headers as $key => $value) {
            $entity->message = str_replace('{{' . $value . '}}', $row_data[$key], $entity->message);
        }

        foreach($entity->tables as $table) {
            $entity->message = str_replace($table->shortcode, $this->parseTableCode($table->columns, $entity->similar, $headers, $json), $entity->message);
        }

        return $entity->message;
    }

    protected function parseTableCode($columns, $similar, $headers, $json) {
        $columns = explode('|', $columns);

        $table_code = '<table><thead><tr>';
        
        foreach($columns as $col) {
            $table_code .= '<th>' . $headers[$col] . '</th>';
        }
        
        $table_code .= '</tr></thead><tbody>';
        
        foreach($similar as $row) {
            $table_code .= '<tr>';
            foreach($columns as $col) {
                $table_code .= '<td>' . $json[$row][$col] . '</td>';
            }
            $table_code .= '</tr>';
        }
        
        $table_code .= '</tbody></table>';
        
        return $table_code;
    }

    public function index() {
        echo 'index function if needed';
    }

    public function upload() {
        $file = $_FILES['fileToUpload']['tmp_name'];
        $filename = $_FILES['fileToUpload']['name'] . '_' . date('mdY');

        $csv = file_get_contents($file);
        
        $json = $this->parseCsv($csv);

        file_put_contents('../public/json/' . $filename . '.json', $json);

        $decoded = json_decode($json);

        $this->render('back/upload.php', ['json' => $decoded, 'filename' => $filename]);

    }

    public function generatePreview() {
        $filename = $_POST['filename'];
        $json = json_decode(file_get_contents('../public/json/' . $filename . '.json'));

        $headers = $json[0];
        
        array_shift($json);
        array_pop($json);

        $number_of_rows = count($json) - 1;

        usort($json, function($a, $b) {
            return strcmp($a[8], $b[8]);
        });

        $sorted = [];

        for($i = 0; $i < $number_of_rows; $i++) {
            $current_email = $json[$i][8];

            $entity = new \stdClass();
            $entity->recipient = $json[$i][8];
            $entity->similar = [$i];
            $entity->filename = $filename;
            $entity->subject = $_POST['subject'];
            $entity->message = $_POST['message'];
            $entity->tables = json_decode($_POST['tables']);

            $j = $i + 1;

            if($j < $number_of_rows) {
                $next_email = $json[$j][8];
            
                while($current_email == $next_email) {
                    array_push($entity->similar, $j);
                    $j = $j + 1;
                    if($j < $number_of_rows) {
                        $next_email = $json[$j][8];
                    } else {
                        $next_email = '';
                    }
                }
            }
            $i = $j;
            array_push($sorted, $entity);
        }

        foreach($sorted as &$entity) {
            $entity->message = $this->parseShortCodes($entity, $headers, $json);
        }

        echo json_encode($sorted);

    }
}