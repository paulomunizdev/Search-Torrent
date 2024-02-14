<!-- 
    PirateWave
    This page utilizes a simple interface to search and display torrent results.
    It allows users to search for torrents, select categories, and subcategories,
    and presents the search results in a tabular format with pagination.
-->

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
        <label for="category">Category:</label>
        <select id="category" name="category">
            <option value="">Select Category</option>
            <option value="100">Audio</option>
            <option value="200">Video</option>
            <option value="300">Applications</option>
            <option value="400">Games</option>
            <option value="500">Porn</option>
            <option value="600">Other</option>
        </select>
        <label for="subcategory">Subcategory:</label>
        <select id="subcategory" name="subcategory">
            <option value="">Select Subcategory</option>
        </select>
        <button type="submit">Search</button>
    </form>
    <br>

    <script>
        document.getElementById('category').addEventListener('change', function() {
            var category = this.value;
            var subcategorySelect = document.getElementById('subcategory');
            subcategorySelect.innerHTML = ''; // Limpa as opções existentes
            if (category !== '') {
                var subcategories = {
                    100: {"101": "Music", "102": "Audio Books", "103": "Sound Clips", "104": "FLAC", "199": "Other"},
                    200: {"201": "Movies", "202": "Movies DVDR", "203": "Music Videos", "204": "Movie Clips", "205": "TV Shows", "206": "Handheld", "207": "HD - Movies", "208": "HD - TV Shows", "209": "3D", "299": "Other"},
                    300: {"301": "Windows", "302": "Mac", "303": "UNIX", "304": "Handheld", "305": "IOS (iPad/iPhone)", "306": "Android", "399": "Other"},
                    400: {"401": "PC", "402": "Mac", "403": "PSx", "404": "XBOX360", "405": "Wii", "406": "Handheld", "407": "IOS (iPad/iPhone)", "408": "Android", "499": "Other"},
                    500: {"501": "Movies", "502": "Movies DVDR", "503": "Pictures", "504": "Games", "505": "HD - Movies", "506": "Movie Clips", "599": "Other"},
                    600: {"601": "E-books", "602": "Comics", "603": "Pictures", "604": "Covers", "605": "Physibles", "699": "Other"}
                };
                for (var subcatId in subcategories[category]) {
                    var option = document.createElement('option');
                    option.value = subcatId;
                    option.textContent = subcategories[category][subcatId];
                    subcategorySelect.appendChild(option);
                }
            }
        });
    </script>

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
        $category = isset($_GET['category']) && $_GET['category'] !== "" ? $_GET['category'] : null;
        $subcategory = isset($_GET['subcategory']) && $_GET['subcategory'] !== "" ? $_GET['subcategory'] : null;

        // Adjusted API URL based on category and subcategory
        $api_url = "http://piratewave.site/api.php?q={$search_query}&page={$page}";
        if ($category) {
            $api_url .= "&category={$category}";
        }
        if ($subcategory) {
            $api_url .= "&subcategory={$subcategory}";
        }
    
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
                echo "<tr><th>Category</th><th>Title</th><th>Seeders</th><th>Leechers</th><th>Size</th><th>Download</th></tr>";
                foreach($torrents as $torrent) {
                    $categoryId = $torrent['category'];
                    $subcategoryId = $torrent['subcategory'];
                    
                    $categories = array(
                        100 => "Audio",
                        200 => "Video",
                        300 => "Applications",
                        400 => "Games",
                        500 => "Porn",
                        600 => "Other"
                    );
                    
                    $subcategories = array(
                        101 => "Music",
                        102 => "Audio Books",
                        103 => "Sound Clips",
                        104 => "FLAC",
                        105 => "Other",
                        201 => "Movies",
                        202 => "Movies DVDR",
                        203 => "Music Videos",
                        204 => "Movie Clips",
                        205 => "TV Shows",
                        206 => "Handheld",
                        207 => "HD - Movies",
                        208 => "HD - TV Shows",
                        209 => "3D",
                        210 => "Other",
                        301 => "Windows",
                        302 => "Mac",
                        303 => "UNIX",
                        304 => "Handheld",
                        305 => "IOS (iPad/iPhone)",
                        306 => "Android",
                        307 => "Other",
                        401 => "PC",
                        402 => "Mac",
                        403 => "PSx",
                        404 => "XBOX360",
                        405 => "Wii",
                        406 => "Handheld",
                        407 => "IOS (iPad/iPhone)",
                        408 => "Android",
                        409 => "Other",
                        501 => "Movies",
                        502 => "Movies DVDR",
                        503 => "Pictures",
                        504 => "Games",
                        505 => "HD - Movies",
                        506 => "Movie Clips",
                        507 => "Other",
                        601 => "E-books",
                        602 => "Comics",
                        603 => "Pictures",
                        604 => "Covers",
                        605 => "Physibles",
                        606 => "Other"
                    );
                    
                    $categoryName = isset($categories[$categoryId]) ? $categories[$categoryId] : "Unknown";
                    $subcategoryName = isset($subcategories[$subcategoryId]) ? $subcategories[$subcategoryId] : "Unknown";
                    
                    $categoryAndSubcategory = "{$categoryName} > {$subcategoryName}";
                    
                    $size = formatSizeUnits($torrent['size']);
                    echo "<tr>";
                    echo "<td>{$categoryAndSubcategory}</td>";
                    echo "<td>{$torrent['title']}</td>";
                    echo "<td>{$torrent['seeders']}</td>";
                    echo "<td>{$torrent['leechers']}</td>";
                    echo "<td>{$size}</td>";
                    echo "<td><a href='{$torrent['magnet']}'><button>Download</button></a></td>";
                    echo "</tr>";
                }
                echo "</table>";
    
                echo "<br>";
                if ($page > 1) {
                    $prev_page = $page - 1;
                    echo "<a href='?search={$search_query}&page={$prev_page}&category={$category}&subcategory={$subcategory}'>Previous Page</a> ";
                }
                if (count($torrents) >= 33) {
                    $next_page = $page + 1;
                    echo "<a href='?search={$search_query}&page={$next_page}&category={$category}&subcategory={$subcategory}'>Next Page</a>";
                }
            }
        } catch (Exception $e) {
            echo "No torrents found!";
        }
    }
    ?>
</body>
</html>
