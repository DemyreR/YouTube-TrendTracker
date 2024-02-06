<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Current Trends</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
        .container {
            background-color: #ddd;
            width: 50%;
            margin: auto;
            border: 20px solid #ddd;
            border-radius: 20px;
            margin-top: 40px;
        }
        .container2 {
            background-color: #ddd;
            width: 50%;
            margin: auto;
            border: 20px solid #ddd;
            border-radius: 20px;
            margin-top: 40px;
            margin-bottom: 40px;
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

    <div class="container">
        <canvas id="videoTypeChart"></canvas>
    </div>

    <div class="container2">
        <canvas id="pieChart"></canvas>
    </div>
    
    <?php
    $host = 'localhost';
    $username = 'root';
    $password = '';
    $database = 'isp_termproject';

    $connection = mysqli_connect($host, $username, $password, $database);

    if (!$connection) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $query = "SELECT video_type, COUNT(*) as count FROM trendingcurrent GROUP BY video_type";
    $result = mysqli_query($connection, $query);

    $labels = [];
    $data = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $labels[] = $row['video_type'];
        $data[] = $row['count'];
    }

    $queryPie = "SELECT video_type, COUNT(*) as count FROM trendingcurrent GROUP BY video_type";
    $resultPie = mysqli_query($connection, $queryPie);

    $labelsPie = [];
    $dataPie = [];

    while ($rowPie = mysqli_fetch_assoc($resultPie)) {
        $labelsPie[] = $rowPie['video_type'];
        $dataPie[] = $rowPie['count'];
    }


    mysqli_close($connection);
    ?>

    <script>
        var ctx = document.getElementById('videoTypeChart').getContext('2d');
        var videoTypeChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($labels); ?>,
                datasets: [{
                    label: 'Number of Videos',
                    data: <?php echo json_encode($data); ?>,
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(153, 102, 255, 0.2)',
                        'rgba(255, 159, 64, 0.2)',
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)',
                    ],
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

    <script>
        var ctxPie = document.getElementById('pieChart').getContext('2d');
        var pieChart = new Chart(ctxPie, {
            type: 'pie',
            data: {
                labels: <?php echo json_encode($labelsPie); ?>,
                datasets: [{
                    data: <?php echo json_encode($dataPie); ?>,
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(153, 102, 255, 0.2)',
                        'rgba(255, 159, 64, 0.2)',
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)',
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                aspectRatio: .66,
            }
        });
    </script>


</body>
</html>
