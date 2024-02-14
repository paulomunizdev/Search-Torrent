<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Torrent Search</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h1>Torrent Search</h1>
    <form method="GET">
        <label for="search">Search Torrents:</label>
        <input type="text" id="search" name="search">
        <button type="submit">Search</button>
    </form>
    <br>
    <?php
    function formatSizeUnits($bytes){
        $units = array('bytes', 'KB', 'MB', 'GB', 'TB');
        $index = 0;
        while ($bytes >= 1024 && $index < 4) {
            $bytes /= 1024;
            $index++;
        }
        return round($bytes, 2) . ' ' . $units[$index];
    }
    
    if(isset($_GET['search']) && !empty($_GET['search'])) {
        $search_query = urlencode($_GET['search']);
        $page = isset($_GET['page']) ? $_GET['page'] : 1;
        $api_url = "http://piratewave.site/api.php?q={$search_query}&page={$page}";
    
        try {
            $response = @file_get_contents($api_url);
            if ($response === false) {
                throw new Exception("Failed to fetch data from the API.");
            }
    
            $torrents = json_decode($response, true);
    
            if(!$torrents || count($torrents) === 0) {
                echo "No torrents found.";
            } else {
                echo "<table>";
                echo "<tr><th>Title</th><th>Seeders</th><th>Leechers</th><th>Size</th><th>Download</th></tr>";
                foreach($torrents as $torrent) {
                    $size = formatSizeUnits($torrent['size']);
                    echo "<tr>";
                    echo "<td>{$torrent['title']}</td>";
                    echo "<td>{$torrent['seeders']}</td>";
                    echo "<td>{$torrent['leechers']}</td>";
                    echo "<td>{$size}</td>";
                    echo "<td><a href='{$torrent['magnet']}'><button>Download</button></a></td>";
                    echo "</tr>";
                }
                echo "</table>";
    
                // Navegação entre páginas
                echo "<br>";
                if ($page > 1) {
                    $prev_page = $page - 1;
                    echo "<a href='?search={$search_query}&page={$prev_page}'>Previous Page</a> ";
                }
                if (count($torrents) >= 33) {
                    $next_page = $page + 1;
                    echo "<a href='?search={$search_query}&page={$next_page}'>Next Page</a>";
                }
            }
        } catch (Exception $e) {
            //echo "Error: No" . $e->getMessage();
            echo "No torrents found!";
        }
    }
    ?>
</body>
</html>
