<?php
require_once 'config.php';

function getCreateTable($pdo, $tableName)
{
    $stmt = $pdo->query("SHOW CREATE TABLE $tableName");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['Create Table'] . ";\n\n";
}

function getDataDump($pdo, $tableName)
{
    $stmt = $pdo->query("SELECT * FROM $tableName");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $output = "";
    if (!empty($rows)) {
        $output .= "INSERT INTO `$tableName` (";
        $columns = array_keys($rows[0]);
        $output .= implode(", ", array_map(function ($col) {
            return "`$col`"; }, $columns));
        $output .= ") VALUES \n";

        $values = [];
        foreach ($rows as $row) {
            $rowValues = array_map(function ($val) use ($pdo) {
                if ($val === null)
                    return "NULL";
                return $pdo->quote($val);
            }, $row);
            $values[] = "(" . implode(", ", $rowValues) . ")";
        }

        $output .= implode(",\n", $values) . ";\n\n";
    }
    return $output;
}

try {
    $sqlOutput = "-- SQL Dump for Food Service Tables\n\n";

    // food_menu structure and data
    $sqlOutput .= "-- Structure for table `food_menu`\n";
    $sqlOutput .= "DROP TABLE IF EXISTS `food_menu`;\n";
    $sqlOutput .= getCreateTable($pdo, 'food_menu');

    $sqlOutput .= "-- Data for table `food_menu`\n";
    $sqlOutput .= getDataDump($pdo, 'food_menu');

    // user_food_selections structure
    $sqlOutput .= "-- Structure for table `user_food_selections`\n";
    $sqlOutput .= "DROP TABLE IF EXISTS `user_food_selections`;\n";
    $sqlOutput .= getCreateTable($pdo, 'user_food_selections');

    // Output to file
    file_put_contents('food_service_dump.sql', $sqlOutput);
    echo "SQL dump created successfully at food_service_dump.sql";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>