<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Popular Tags</title>
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
            height: 500px;
        }
        .container2 {
            background-color: #ddd;
            width: 50%;
            margin: auto;
            border: 20px solid #ddd;
            border-radius: 20px;
            margin-top: 40px;
            height: 100%;
            text-align: center;
            margin-bottom: 40px;
        }
        .container3 {
            background-color: #ddd;
            width: 50%;
            margin: auto;
            border: 20px solid #ddd;
            border-radius: 20px;
            margin-top: 40px;
            height: 100%;
            text-align: center;
        }
        table {
            border-radius: 20px;
            width: 100%;
            margin: auto;
            border: 20px solid #ddd;
            background-color: #ddd;
            margin-bottom: 30px;
        }
        th, td {
            border: 1px solid gray;
            padding: 12px;
            text-align: left;
            
        }

    </style>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/d3/3.5.17/d3.min.js"></script>
    <script src="https://cdn.rawgit.com/jasondavies/d3-cloud/v1.2.5/build/d3.layout.cloud.js"></script>
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
        <svg id="tagsWordCloud" width="100%" height="100%"></svg>
    </div>

    <div class="container3">
        <h2>Top 15 Most Used Tags</h2>
        <table>
            <thead>
                <tr>
                    <th>Tag</th>
                    <th>Video Count</th>
                </tr>
            </thead>
            <tbody id="topUsedTagsList"></tbody>
        </table>
    </div>

    <div class="container2">
        <h2>Top 15 Most Viewed Tags</h2>
        <table>
            <thead>
                <tr>
                    <th>Tag</th>
                    <th>Total Views</th>
                </tr>
            </thead>
            <tbody id="topTagsList"></tbody>
        </table>
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

    $query = "SELECT tags FROM trendingcurrent";
    $result = mysqli_query($connection, $query);

    $tagCounts = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $videoTags = explode(',', $row['tags']);
        foreach ($videoTags as $tag) {
            $tag = trim($tag);
            $tagCounts[$tag] = isset($tagCounts[$tag]) ? $tagCounts[$tag] + 1 : 1;
        }
    }

    arsort($tagCounts);

    $topTags = array_slice($tagCounts, 0, 1000, true);

    mysqli_close($connection);
    ?>

    <script>
        var tagsData = <?php echo json_encode($topTags); ?>;
        var maxCount = Math.max(...Object.values(tagsData));

        var tags = Object.keys(tagsData).map(function(tag) {
            return { text: tag, size: 10 + (tagsData[tag] / maxCount) * 180 };
        });

        var wordCloud = d3.layout.cloud()
            .size([document.getElementById('tagsWordCloud').clientWidth, document.getElementById('tagsWordCloud').clientHeight])
            .words(tags)
            .padding(5)
            .rotate(function() { return ~~(Math.random() * 2) * 90; })
            .font("Impact")
            .fontSize(function(d) { return d.size; })
            .on("end", draw);

        wordCloud.start();

        function draw(words) {
            d3.select("#tagsWordCloud").append("g")
                .attr("transform", "translate(" + document.getElementById('tagsWordCloud').clientWidth / 2 + "," + document.getElementById('tagsWordCloud').clientHeight / 2 + ")")
                .selectAll("text")
                .data(words)
                .enter().append("text")
                .style("font-size", function(d) { return d.size + "px"; })
                .style("font-family", "Impact")
                .style("fill", "black")
                .attr("text-anchor", "middle")
                .attr("transform", function(d) {
                    return "translate(" + [d.x, d.y] + ")rotate(" + d.rotate + ")";
                })
                .text(function(d) { return d.text; });
        }
    </script>

    <?php
    $host = 'localhost';
    $username = 'root';
    $password = '';
    $database = 'isp_termproject';

    $connection = mysqli_connect($host, $username, $password, $database);

    if (!$connection) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $query = "SELECT tags, view_count FROM trendingcurrent";
    $result = mysqli_query($connection, $query);

    $tagCounts = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $videoTags = explode(',', $row['tags']);

        $videoTags = array_filter($videoTags, function($tag) {
            return trim($tag) !== '';
        });

        if (!empty($videoTags)) {
            $tag = trim($videoTags[0]);
            if ($tag !== "2024")
            {
                $tagCounts[$tag]['count'] = isset($tagCounts[$tag]['count']) ? $tagCounts[$tag]['count'] + 1 : 1;
                $tagCounts[$tag]['views'] = isset($tagCounts[$tag]['views']) ? $tagCounts[$tag]['views'] + $row['view_count'] : $row['view_count'];
            }
        }
    }

    uasort($tagCounts, function ($a, $b) {
        return $b['views'] <=> $a['views'];
    });

    $topTags = array_slice($tagCounts, 0, 15, true);

    mysqli_close($connection);
    ?>

    <script>
        var topTagsList = d3.select("#topTagsList");
        var topTagsData = <?php echo json_encode($topTags); ?>;
        
        topTagsList.selectAll("tr")
            .data(Object.keys(topTagsData))
            .enter().append("tr")
            .html(function(d) {
                return "<td>" + d + "</td><td>" + topTagsData[d]['views'] + "</td>";
            });
    </script>

    <?php
    $host = 'localhost';
    $username = 'root';
    $password = '';
    $database = 'isp_termproject';

    $connection = mysqli_connect($host, $username, $password, $database);

    if (!$connection) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $query = "SELECT tags FROM trendingcurrent";
    $result = mysqli_query($connection, $query);

    $tagCounts = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $videoTags = explode(',', $row['tags']);
        $videoTags = array_filter($videoTags, function($tag) {
            return trim($tag) !== '';
        });

        foreach ($videoTags as $tag) {
            $tag = trim($tag);
            $tagCounts[$tag] = isset($tagCounts[$tag]) ? $tagCounts[$tag] + 1 : 1;
        }
    }

    arsort($tagCounts);

    $topTags = array_slice($tagCounts, 0, 15, true);

    mysqli_close($connection);
    ?>

    <script>
        var topUsedTagsList = d3.select("#topUsedTagsList");
        var topUsedTagsData = <?php echo json_encode($topTags); ?>;
        
        topUsedTagsList.selectAll("tr")
            .data(Object.keys(topUsedTagsData))
            .enter().append("tr")
            .html(function(d) {
                return "<td>" + d + "</td><td>" + topUsedTagsData[d] + "</td>";
            });
    </script>

</body>
</html>
