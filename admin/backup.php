<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

checkLogin();

try {
    $database = new Database();
    $db = $database->getConnection();
    $db_name = $database->getDbName();

    $tables = [];
    $result = $db->query("SHOW TABLES");
    while ($row = $result->fetch(PDO::FETCH_NUM)) {
        $tables[] = $row[0];
    }

    $sql_dump = "";
    foreach ($tables as $table) {
        $result = $db->query("SELECT * FROM " . $table);
        $num_fields = $result->columnCount();
        
        $sql_dump .= "DROP TABLE IF EXISTS `" . $table . "`;\n";
        
        $create_table_result = $db->query("SHOW CREATE TABLE " . $table)->fetch(PDO::FETCH_ASSOC);
        $sql_dump .= "\n\n" . $create_table_result['Create Table'] . ";\n\n";
        
        for ($i = 0; $i < $result->rowCount(); $i++) {
            $row = $result->fetch(PDO::FETCH_ASSOC);
            $sql_dump .= "INSERT INTO " . $table . " VALUES(";
            $j = 0;
            foreach ($row as $cell) {
                if ($cell === null) {
                    $sql_dump .= 'NULL';
                } else {
                    $cell = addslashes($cell);
                    $cell = preg_replace("/\n/","\\n",$cell);
                    $sql_dump .= '"' . $cell . '"';
                }
                if ($j < ($num_fields - 1)) {
                    $sql_dump .= ',';
                }
                $j++;
            }
            $sql_dump .= ");\n";
        }
        $sql_dump .= "\n\n\n";
    }

    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="backup-' . $db_name . '-' . date('Y-m-d_H-i-s') . '.sql"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . strlen($sql_dump));
    echo $sql_dump;
    exit();

} catch (Exception $e) {
    die("Error creating backup: " . $e->getMessage());
}
