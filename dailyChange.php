<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daily Change</title>
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
    <button onclick="showTable('videoType')">Video Type Count Changes</button>
    <button onclick="showTable('avgView')">Average View Count Changes</button>
    <button onclick="showTable('avgLike')">Average Like Count Changes</button>
</div>

<div id="videoTypeTable">
  <?php
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

  $result_current = $conn->query($sql_current);
  $result_yesterday = $conn->query($sql_yesterday);

  $current_counts = $yesterday_counts = array();

  while ($row = $result_current->fetch_assoc()) {
      $current_counts[$row['video_type']] = $row['count_current'];
  }

  while ($row = $result_yesterday->fetch_assoc()) {
      $yesterday_counts[$row['video_type']] = $row['count_yesterday'];
  }

  echo "<table>";
  echo "<tr><th>Video Type</th><th>Video Type Count Change</th></tr>";

  foreach ($current_counts as $video_type => $current_count) {
      $yesterday_count = isset($yesterday_counts[$video_type]) ? $yesterday_counts[$video_type] : 0;
      $change = $current_count - $yesterday_count;
      $percentage_change = round(($change / $yesterday_count) * 100, 2);

      $class = ($change > 0) ? 'increase' : (($change < 0) ? 'decrease' : 'no-change');
      $arrow = ($change > 0) ? '&#9650;' : (($change < 0) ? '&#9660;' : '■');

      echo "<tr>";
      echo "<td>$video_type</td>";
      echo "<td class='$class'>$change ($percentage_change%) $arrow</td>";
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
  
  $result_current = $conn->query($sql_current);
  $result_yesterday = $conn->query($sql_yesterday);

  $current_avg_like_counts = $yesterday_avg_like_counts = array();

  while ($row = $result_current->fetch_assoc()) {
      $current_avg_like_counts[$row['video_type']] = $row['avg_like_count_current'];
  }

  while ($row = $result_yesterday->fetch_assoc()) {
      $yesterday_avg_like_counts[$row['video_type']] = $row['avg_like_count_yesterday'];
  }

  echo "<table>";
  echo "<tr><th>Video Type</th><th>Average Like Count Change</th></tr>";
  
  foreach ($current_avg_like_counts as $video_type => $current_avg_like_count) {
      $yesterday_avg_like_count = isset($yesterday_avg_like_counts[$video_type]) ? $yesterday_avg_like_counts[$video_type] : 0;
      $change = $current_avg_like_count - $yesterday_avg_like_count;
      $percentage_change = round(($change / $yesterday_avg_like_count) * 100, 2);

      $class = ($change > 0) ? 'increase' : (($change < 0) ? 'decrease' : 'no-change');
      $arrow = ($change > 0) ? '&#9650;' : (($change < 0) ? '&#9660;' : '■');

      echo "<tr>";
      echo "<td>$video_type</td>";
      echo "<td class='$class'>$change ($percentage_change%) $arrow</td>";
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
  
  $result_current = $conn->query($sql_current);
  $result_yesterday = $conn->query($sql_yesterday);

  $current_avg_counts = $yesterday_avg_counts = array();

  while ($row = $result_current->fetch_assoc()) {
      $current_avg_counts[$row['video_type']] = $row['avg_view_count_current'];
  }

  while ($row = $result_yesterday->fetch_assoc()) {
      $yesterday_avg_counts[$row['video_type']] = $row['avg_view_count_yesterday'];
  }

  echo "<table>";
  echo "<tr><th>Video Type</th><th>Average View Count Change</th></tr>";
  
  foreach ($current_avg_counts as $video_type => $current_avg_count) {
      $yesterday_avg_count = isset($yesterday_avg_counts[$video_type]) ? $yesterday_avg_counts[$video_type] : 0;
      $change = $current_avg_count - $yesterday_avg_count;
      $percentage_change = round(($change / $yesterday_avg_count) * 100, 2);

      $class = ($change > 0) ? 'increase' : (($change < 0) ? 'decrease' : 'no-change');
      $arrow = ($change > 0) ? '&#9650;' : (($change < 0) ? '&#9660;' : '■');

      echo "<tr>";
      echo "<td>$video_type</td>";
      echo "<td class='$class'>$change ($percentage_change%) $arrow</td>";
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
