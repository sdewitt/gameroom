<?php
include_once '../config.php';
ini_set('display_errors', 1);
error_reporting(E_ALL);
        WHERE g.showyear = '" . PRIOR_YEAR . "' AND g.approved = 1
define('db_user','u0vunj7bxc6ww');
define('db_pass','f8lmh2l15m2m');
define('db_name','db0fnwzcvwqnvk');
define('db_charset','utf8');

try {
    $dsn = "mysql:host=" . db_host . ";dbname=" . db_name . ";charset=" . db_charset;
    $pdo = new PDO($dsn, db_user, db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("
        SELECT 
            a.id AS ownerid,
            a.firstname,
            a.lastname,
            g.gametitle
        FROM gamelist g
        JOIN accounts a ON g.ownerid = a.id
        WHERE g.showyear = '2025' AND g.approved = 1
        ORDER BY a.lastname, a.firstname, g.gametitle
    ");
    $stmt->execute();    echo "<!DOCTYPE html><html><head><title>Game Count Report " . PRIOR_YEAR . "</title>
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Organize by owner
    $owners = [];
    foreach ($rows as $row) {
        $oid = $row['ownerid'];
        if (!isset($owners[$oid])) {
            $owners[$oid] = [
                'name' => "{$row['firstname']} {$row['lastname']}",
                'games' => []
            ];
        }
        $owners[$oid]['games'][] = $row['gametitle'];
    }

    // Buckets with renamed labels
    $categories = [
        'Bringing 1 game' => [],
        'Bringing 2 games' => [],
        'Bringing 3 games' => [],
        'Bringing 4 or 5 games' => [],
        'Bringing 6 or 7 games' => [],
        'Bringing 8 or 9 games' => [],
        'Bringing 10+ games' => []
    ];

    foreach ($owners as $oid => $owner) {
        $count = count($owner['games']);
        $entry = [
            'id' => $oid,
            'name' => "{$owner['name']} - $count",
            'games' => $owner['games']
        ];
    echo "<h1>Game Count Report for " . PRIOR_YEAR . "</h1>";

        if ($count === 1) {
            $categories['Bringing 1 game'][] = $entry;
        } elseif ($count === 2) {
            $categories['Bringing 2 games'][] = $entry;
        } elseif ($count === 3) {
            $categories['Bringing 3 games'][] = $entry;
        } elseif ($count >= 4 && $count <= 5) {
            $categories['Bringing 4 or 5 games'][] = $entry;
        } elseif ($count >= 6 && $count <= 7) {
            $categories['Bringing 6 or 7 games'][] = $entry;
        } elseif ($count >= 8 && $count <= 9) {
            $categories['Bringing 8 or 9 games'][] = $entry;
        } elseif ($count >= 10) {
            $categories['Bringing 10+ games'][] = $entry;
        }
    }

    echo "<!DOCTYPE html><html><head><title>Game Count Report 2025</title>
<style>
    body { font-family: Arial, sans-serif; padding: 20px; }
    h1 { font-size: 28px; }
    h2 { margin-top: 30px; }
    ul { padding-left: 0; }
    li { list-style: none; margin-bottom: 10px; }

    .entry-line {
        display: flex;
        align-items: center;
        cursor: pointer;
        font-weight: bold;
        padding: 4px 8px;
        border-radius: 5px;
    }

    .entry-line:hover {
        background-color: #f0f0f0;
    }

    .toggle-icon {
        width: 1.5em;
        display: inline-block;
        text-align: center;
        margin-right: 8px;
        font-size: 18px;
    }

    .games-list {
        display: none;
        margin-left: 24px;
        margin-top: 6px;
    }

    .category-header {
        font-size: 18px;
        margin-top: 5px;
        margin-bottom: 10px;
        cursor: pointer;
        font-weight: normal;
    }

    .category-content {
        display: none;
        margin-left: 20px;
    }
</style>
<script>
    function toggleGames(id) {
        const games = document.getElementById('games-' + id);
        const toggle = document.getElementById('toggle-' + id);
        const isOpen = games.style.display === 'block';
        games.style.display = isOpen ? 'none' : 'block';
        toggle.innerText = isOpen ? '➕' : '➖';
    }

    function toggleCategory(id) {
        const section = document.getElementById('section-' + id);
        const icon = document.getElementById('section-toggle-' + id);
        const isOpen = section.style.display === 'block';
        section.style.display = isOpen ? 'none' : 'block';
        icon.innerText = isOpen ? '➕' : '➖';
    }
</script>
</head><body>";

    echo "<h1>Game Count Report for 2025</h1>";

    $sectionCounter = 0;
    foreach ($categories as $label => $entries) {
        $peopleCount = count($entries);
        $gameTotal = array_sum(array_map(function($e) {
            return count($e['games']);
        }, $entries));
        $sectionID = $sectionCounter++;

        echo "<h2>$label</h2>";

        echo "<div class='category-header' onclick='toggleCategory($sectionID)'>
                <span class='toggle-icon' id='section-toggle-$sectionID'>➕</span>
                $peopleCount Game Bringers / $gameTotal Machines
              </div>";

        echo "<div class='category-content' id='section-$sectionID'>";
        if ($peopleCount === 0) {
            echo "<p><em>No users in this category.</em></p>";
        } else {
            echo "<ul>";
            foreach ($entries as $entry) {
                $uid = $entry['id'];
                echo "<li>
                        <div class='entry-line' onclick='toggleGames($uid)'>
                            <span class='toggle-icon' id='toggle-$uid'>➕</span>
                            <span>{$entry['name']}</span>
                        </div>
                        <ul class='games-list' id='games-$uid'>";
                foreach ($entry['games'] as $game) {
                    echo "<li>$game</li>";
                }
                echo "</ul></li>";
            }
            echo "</ul>";
        }
        echo "</div>";
    }

    echo "</body></html>";

} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
