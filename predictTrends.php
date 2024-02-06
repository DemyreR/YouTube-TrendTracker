<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Predicted Change</title>
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
        text-align: center;
        margin-top: 20px;
        padding-bottom: 30px;
        padding-top: 10px;
    }
    button {
        margin: 5px;
        padding: 10px;
        font-size: 14px;
        width: 16.15%;
    }
    .data-table {
        border-collapse: collapse;
        width: 50%;
        margin: 20px auto;
        border: 10px solid gray;
        border-radius: 8px;
        overflow: hidden;
    }
    th, td {
        border: 1px solid gray;
        padding: 12px;
        text-align: left;
        
    }
    .increase { color: green; }
    .decrease { color: red; }
    .no-change { color: gray; }
    table {
        border-radius: 20px;
        width: 50%;
        margin: auto;
        border: 20px solid #ddd;
        background-color: #ddd;
        margin-bottom: 30px;
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
<button onclick="showTable('videoType')">Predicted Video Type Count Changes</button>
<button onclick="showTable('avgView')">Predicted Average View Count Changes</button>
<button onclick="showTable('avgLike')">Predicted Average Like Count Changes</button>
</div>

    <div id="videoTypeTable">
        <?php
        // PHP-ML library
        require 'vendor/autoload.php';

        $servername = "localhost";
        $username = "root";
        $password = "";
        $database = "isp_termproject";

        $conn = new mysqli($servername, $username, $password, $database);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $sql_current = "SELECT video_type, COUNT(*) AS count_current FROM trendingcurrent GROUP BY video_type";
        $sql_yesterday = "SELECT video_type, COUNT(*) AS count_yesterday FROM trendingyesterday GROUP BY video_type";
        $sql_2days_ago = "SELECT video_type, COUNT(*) AS count_2days_ago FROM trending2daysago GROUP BY video_type";

        $result_current = $conn->query($sql_current);
        $result_yesterday = $conn->query($sql_yesterday);
        $result_2days_ago = $conn->query($sql_2days_ago);

        $current_counts = $yesterday_counts = $counts_2days_ago = array();

        while ($row = $result_current->fetch_assoc()) {
            $current_counts[$row['video_type']] = $row['count_current'];
        }

        while ($row = $result_yesterday->fetch_assoc()) {
            $yesterday_counts[$row['video_type']] = $row['count_yesterday'];
        }

        while ($row = $result_2days_ago->fetch_assoc()) {
            $counts_2days_ago[$row['video_type']] = $row['count_2days_ago'];
        }

        $trainingData = [];
        $output = [];

        foreach ($current_counts as $video_type => $current_count) {
            $yesterday_count = isset($yesterday_counts[$video_type]) ? $yesterday_counts[$video_type] : 0;
            $count_2days_ago = isset($counts_2days_ago[$video_type]) ? $counts_2days_ago[$video_type] : 0;

            $trainingData[] = [$yesterday_count, $count_2days_ago];
            $output[] = $current_count - $yesterday_count;
        }

        $regression = new Phpml\Regression\LeastSquares();
        $regression->train($trainingData, $output);

        echo "<table>";
        echo "<tr><th>Video Type</th><th>Predicted Video Type Count Change</th></tr>";

        foreach ($current_counts as $video_type => $current_count) {
            $yesterday_count = isset($yesterday_counts[$video_type]) ? $yesterday_counts[$video_type] : 0;
            $count_2days_ago = isset($counts_2days_ago[$video_type]) ? $counts_2days_ago[$video_type] : 0;

            $predictedChange = $regression->predict([$yesterday_count, $count_2days_ago]);

            $roundedChange = round($predictedChange, 2);

            $percentage_change = round(($roundedChange / $current_count) * 100, 2);

            $class = ($roundedChange > 0) ? 'increase' : (($roundedChange < 0) ? 'decrease' : 'no-change');
            $arrow = ($roundedChange > 0) ? '&#9650;' : (($roundedChange < 0) ? '&#9660;' : '■');
            
            echo "<tr>";
            echo "<td>$video_type</td>";
            echo "<td class='$class'>$roundedChange ($percentage_change%) $arrow</td>";
            echo "</tr>";
        }

        echo "</table>";

        $conn->close();
        ?>
    </div>
    
    <div id="avgLikeTable" style="display:none;">
    <?php
    $servername = "localhost";
    $username = "root";
    $password = "";
    $database = "isp_termproject";

    $conn = new mysqli($servername, $username, $password, $database);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql_current = "SELECT video_type, AVG(like_count) AS avg_like_count_current FROM trendingcurrent GROUP BY video_type";
    $sql_yesterday = "SELECT video_type, AVG(like_count) AS avg_like_count_yesterday FROM trendingyesterday GROUP BY video_type";
    $sql_2days_ago = "SELECT video_type, AVG(like_count) AS avg_like_count_2days_ago FROM trending2daysago GROUP BY video_type";

    $result_current = $conn->query($sql_current);
    $result_yesterday = $conn->query($sql_yesterday);
    $result_2days_ago = $conn->query($sql_2days_ago);

    $current_avg_like_counts = $yesterday_avg_like_counts = $avg_like_counts_2days_ago = array();

    while ($row = $result_current->fetch_assoc()) {
        $current_avg_like_counts[$row['video_type']] = $row['avg_like_count_current'];
    }

    while ($row = $result_yesterday->fetch_assoc()) {
        $yesterday_avg_like_counts[$row['video_type']] = $row['avg_like_count_yesterday'];
    }

    while ($row = $result_2days_ago->fetch_assoc()) {
        $avg_like_counts_2days_ago[$row['video_type']] = $row['avg_like_count_2days_ago'];
    }

    $trainingData = [];
    $output = [];

    foreach ($current_avg_like_counts as $video_type => $current_avg_like_count) {
        $yesterday_avg_like_count = isset($yesterday_avg_like_counts[$video_type]) ? $yesterday_avg_like_counts[$video_type] : 0;
        $avg_like_count_2days_ago = isset($avg_like_counts_2days_ago[$video_type]) ? $avg_like_counts_2days_ago[$video_type] : 0;

        $trainingData[] = [$yesterday_avg_like_count, $avg_like_count_2days_ago];
        $output[] = $current_avg_like_count - $yesterday_avg_like_count;
    }

    $regression = new Phpml\Regression\LeastSquares();
    $regression->train($trainingData, $output);

    echo "<table>";
    echo "<tr><th>Video Type</th><th>Predicted Average Like Count Change</th></tr>";

    foreach ($current_avg_like_counts as $video_type => $current_avg_like_count) {
        $yesterday_avg_like_count = isset($yesterday_avg_like_counts[$video_type]) ? $yesterday_avg_like_counts[$video_type] : 0;
        $avg_like_count_2days_ago = isset($avg_like_counts_2days_ago[$video_type]) ? $avg_like_counts_2days_ago[$video_type] : 0;

        $predictedChange = $regression->predict([$yesterday_avg_like_count, $avg_like_count_2days_ago]);
        $percentage_change = round(($predictedChange / $current_avg_like_count) * 100, 2);

        $class = ($predictedChange > 0) ? 'increase' : (($predictedChange < 0) ? 'decrease' : 'no-change');
        $arrow = ($predictedChange > 0) ? '&#9650;' : (($predictedChange < 0) ? '&#9660;' : '■');

        echo "<tr>";
        echo "<td>$video_type</td>";
        echo "<td class='$class'>$predictedChange ($percentage_change%) $arrow</td>";
        echo "</tr>";
    }

    echo "</table>";

    $conn->close();
    ?>
</div>

<div id="avgViewTable" style="display:none;">
  <?php
  $servername = "localhost";
  $username = "root";
  $password = "";
  $database = "isp_termproject";

  $conn = new mysqli($servername, $username, $password, $database);

  if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
  }

  $sql_current = "SELECT video_type, AVG(view_count) AS avg_view_count_current FROM trendingcurrent GROUP BY video_type";
  $sql_yesterday = "SELECT video_type, AVG(view_count) AS avg_view_count_yesterday FROM trendingyesterday GROUP BY video_type";
  $sql_2days_ago = "SELECT video_type, AVG(view_count) AS avg_view_count_2days_ago FROM trending2daysago GROUP BY video_type";

  $result_current = $conn->query($sql_current);
  $result_yesterday = $conn->query($sql_yesterday);
  $result_2days_ago = $conn->query($sql_2days_ago);

  $current_avg_view_counts = $yesterday_avg_view_counts = $avg_view_counts_2days_ago = array();

  while ($row = $result_current->fetch_assoc()) {
      $current_avg_view_counts[$row['video_type']] = $row['avg_view_count_current'];
  }

  while ($row = $result_yesterday->fetch_assoc()) {
      $yesterday_avg_view_counts[$row['video_type']] = $row['avg_view_count_yesterday'];
  }

  while ($row = $result_2days_ago->fetch_assoc()) {
      $avg_view_counts_2days_ago[$row['video_type']] = $row['avg_view_count_2days_ago'];
  }

  $trainingData = [];
  $output = [];

  foreach ($current_avg_view_counts as $video_type => $current_avg_view_count) {
      $yesterday_avg_view_count = isset($yesterday_avg_view_counts[$video_type]) ? $yesterday_avg_view_counts[$video_type] : 0;
      $avg_view_count_2days_ago = isset($avg_view_counts_2days_ago[$video_type]) ? $avg_view_counts_2days_ago[$video_type] : 0;

      $trainingData[] = [$yesterday_avg_view_count, $avg_view_count_2days_ago];
      $output[] = $current_avg_view_count - $yesterday_avg_view_count;
  }

  $regression = new Phpml\Regression\LeastSquares();
  $regression->train($trainingData, $output);

  echo "<table>";
  echo "<tr><th>Video Type</th><th>Predicted Average View Count Change</th></tr>";

  foreach ($current_avg_view_counts as $video_type => $current_avg_view_count) {
      $yesterday_avg_view_count = isset($yesterday_avg_view_counts[$video_type]) ? $yesterday_avg_view_counts[$video_type] : 0;
      $avg_view_count_2days_ago = isset($avg_view_counts_2days_ago[$video_type]) ? $avg_view_counts_2days_ago[$video_type] : 0;

      $predictedChange = $regression->predict([$yesterday_avg_view_count, $avg_view_count_2days_ago]);
      $percentage_change = round(($predictedChange / $current_avg_view_count) * 100, 2);

      $class = ($predictedChange > 0) ? 'increase' : (($predictedChange < 0) ? 'decrease' : 'no-change');
      $arrow = ($predictedChange > 0) ? '&#9650;' : (($predictedChange < 0) ? '&#9660;' : '■');

      echo "<tr>";
      echo "<td>$video_type</td>";
      echo "<td class='$class'>$predictedChange ($percentage_change%) $arrow</td>";
      echo "</tr>";
  }

  echo "</table>";

  $conn->close();
  ?>
</div>

<script>
  function showTable(tableType) {
    document.getElementById('videoTypeTable').style.display = (tableType === 'videoType') ? 'block' : 'none';
    document.getElementById('avgLikeTable').style.display = (tableType === 'avgLike') ? 'block' : 'none';
    document.getElementById('avgViewTable').style.display = (tableType === 'avgView') ? 'block' : 'none';
  }
</script>

</body>
</html>
