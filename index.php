<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Torrent Search</title>
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
        if ($bytes >= 1099511627776){
            $bytes = number_format($bytes / 1099511627776, 2) . ' TB';
        } elseif ($bytes >= 1073741824){
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576){
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024){
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes > 1){
            $bytes = $bytes . ' bytes';
        } elseif ($bytes == 1){
            $bytes = $bytes . ' byte';
        } else{
            $bytes = '0 bytes';
        }
    
        return $bytes;
    }

    if(isset($_GET['search']) && !empty($_GET['search'])) {
        $search_query = urlencode($_GET['search']);
        $page = isset($_GET['page']) ? $_GET['page'] : 1;
        $api_url = "http://piratewave.site/api.php?q={$search_query}&page={$page}";
        
        $response = file_get_contents($api_url);
        $torrents = json_decode($response, true);
        
        if($torrents && count($torrents) > 0) {
            echo "<table border='1'>";
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
        } else {
            echo "No torrents found.";
        }
    }
    ?>
</body>
</html>
