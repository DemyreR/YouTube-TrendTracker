<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Current Top 10</title>
    <script src="path/to/Chart.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #313131;
        }
        header {
            background-color: #f30707;
            color: white;
            padding: 30px;
            text-align: center;
        }
        nav {
            display: flex;
            justify-content: space-around;
            background-color: #bb0404;
            padding: 25px;
            border-bottom-left-radius: 15px;
            border-bottom-right-radius: 15px;
        }
        nav a {
            color: white;
            text-decoration: none;
        }
        .video-container {
            position: relative;
            width: (100% - 20px);
            padding-bottom: 56.25%;
            height: 0;
            overflow: hidden;
            border: 20px solid #ddd;
            margin-top: 40px;
            border-top-left-radius: 20px;
            border-top-right-radius: 20px;
        }

        .video-container iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: #ddd;
        }

        .video-info {
            margin-bottom: 40px;
            padding: 10px;
            margin-top: -20px;
            border: 20px solid #ddd;
            border-bottom-left-radius: 20px;
            border-bottom-right-radius: 20px;
            background-color: #ddd;
            text-align: center;
        }
    </style>
</head>
<body>
    <header>
        <h1>YouTube TrendTracker</h1>
    </header>
    <nav>
        <a href="index.html">Home</a>
        <a href="topTen.php">Current Top 10</a>
        <a href="videoTypes.php">Current Trends</a>
        <a href="dailyChange.php">Daily Change</a>
        <a href="predictTrends.php">Predicted Change</a>
        <a href="popularTags.php">Popular Tags</a>
    </nav>

<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "isp_termproject";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$query = "SELECT video_id, title, view_count, like_count FROM trendingcurrent ORDER BY view_count DESC LIMIT 10";
$result = $conn->query($query);

$trendingVideos = [];
while ($row = $result->fetch_assoc()) {
    $trendingVideos[] = $row;
}

$conn->close();
?>

<div style="width: 50%; margin: auto;">

    <?php foreach ($trendingVideos as $video): ?>
        <div class="video-container">
            <iframe width="560" height="315" src="https://www.youtube.com/embed/<?php echo $video['video_id']; ?>" frameborder="0" allowfullscreen></iframe>
        </div>

        <div class="video-info">
            <h3><?php echo $video['title']; ?></h3>
            <p><strong>View Count:</strong> <?php echo $video['view_count']; ?></p>
            <p><strong>Like Count:</strong> <?php echo $video['like_count']; ?></p>
        </div>
    <?php endforeach; ?>

    <canvas id="trendingChart"></canvas>
</div>

<script>
    var ctx = document.getElementById('trendingChart').getContext('2d');
    var myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode(array_column($trendingVideos, 'title')); ?>,
            datasets: [{
                label: 'View Count',
                data: <?php echo json_encode(array_column($trendingVideos, 'view_count')); ?>,
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>

</body>
</html>
