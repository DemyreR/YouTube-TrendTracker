<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "isp_termproject";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM trendingcurrent";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="trendingcurrent.csv"');

    $output = fopen('php://output', 'w');
    $header = array('video_id', 'title', 'view_count', 'like_count', 'video_type', 'tags', 'date');
    fputcsv($output, $header, ',', '"');

    while ($row = $result->fetch_assoc()) {
        $row = array_map(function ($value) {
            return str_replace('"', "'", $value);
        }, $row);
        fputcsv($output, $row, ',', '"');
    }

    fclose($output);
} else {
    echo "No data found in the database.";
}

$conn->close();
?>