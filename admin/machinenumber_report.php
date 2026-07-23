<?php
include 'main.php';

$stmt = $pdo->prepare('
    SELECT
        a.id AS ownerid,
        a.firstname,
        a.lastname,
        g.gametitle
    FROM gamelist g
    JOIN accounts a ON g.ownerid = a.id
    WHERE g.showyear = ? AND g.approved = 1
    ORDER BY a.lastname, a.firstname, g.gametitle
');
$stmt->execute([CURRENT_YEAR]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$owners = [];
foreach ($rows as $row) {
    $ownerId = (int) $row['ownerid'];
    if (!isset($owners[$ownerId])) {
        $owners[$ownerId] = [
            'name' => trim($row['firstname'] . ' ' . $row['lastname']),
            'games' => []
        ];
    }
    $owners[$ownerId]['games'][] = $row['gametitle'];
}

$categories = [
    'Bringing 1 game' => [],
    'Bringing 2 games' => [],
    'Bringing 3 games' => [],
    'Bringing 4 or 5 games' => [],
    'Bringing 6 or 7 games' => [],
    'Bringing 8 or 9 games' => [],
    'Bringing 10+ games' => []
];

foreach ($owners as $ownerId => $owner) {
    $count = count($owner['games']);
    $entry = [
        'id' => $ownerId,
        'name' => $owner['name'] . ' - ' . $count,
        'games' => $owner['games']
    ];

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

function report_escape($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}
?>
<?=template_admin_header('Game Count Report ' . CURRENT_YEAR, 'dashboard')?>

<style>
    .machine-count-report h1 { font-size: 28px; }
    .machine-count-report h2 { margin-top: 30px; }
    .machine-count-report ul { padding-left: 0; }
    .machine-count-report li { list-style: none; margin-bottom: 10px; }
    .machine-count-report .entry-line {
        display: flex;
        align-items: center;
        cursor: pointer;
        font-weight: bold;
        padding: 4px 8px;
        border-radius: 5px;
    }
    .machine-count-report .entry-line:hover { background-color: #f0f0f0; }
    .machine-count-report .toggle-icon {
        width: 1.5em;
        display: inline-block;
        text-align: center;
        margin-right: 8px;
        font-size: 18px;
    }
    .machine-count-report .games-list {
        display: none;
        margin-left: 24px;
        margin-top: 6px;
    }
    .machine-count-report .category-header {
        font-size: 18px;
        margin-top: 5px;
        margin-bottom: 10px;
        cursor: pointer;
        font-weight: normal;
    }
    .machine-count-report .category-content {
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

<section class="machine-count-report">
    <h1>Game Count Report for <?=report_escape(CURRENT_YEAR)?></h1>

    <?php $sectionCounter = 0; ?>
    <?php foreach ($categories as $label => $entries): ?>
        <?php
        $peopleCount = count($entries);
        $gameTotal = array_sum(array_map(function($entry) {
            return count($entry['games']);
        }, $entries));
        $sectionId = $sectionCounter++;
        ?>
        <h2><?=report_escape($label)?></h2>
        <div class="category-header" onclick="toggleCategory(<?=$sectionId?>)">
            <span class="toggle-icon" id="section-toggle-<?=$sectionId?>">➕</span>
            <?=report_escape($peopleCount)?> Game Bringers / <?=report_escape($gameTotal)?> Machines
        </div>
        <div class="category-content" id="section-<?=$sectionId?>">
            <?php if ($peopleCount === 0): ?>
                <p><em>No users in this category.</em></p>
            <?php else: ?>
                <ul>
                    <?php foreach ($entries as $entry): ?>
                        <?php $ownerId = (int) $entry['id']; ?>
                        <li>
                            <div class="entry-line" onclick="toggleGames(<?=$ownerId?>)">
                                <span class="toggle-icon" id="toggle-<?=$ownerId?>">➕</span>
                                <span><?=report_escape($entry['name'])?></span>
                            </div>
                            <ul class="games-list" id="games-<?=$ownerId?>">
                                <?php foreach ($entry['games'] as $game): ?>
                                    <li><?=report_escape($game)?></li>
                                <?php endforeach; ?>
                            </ul>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</section>

<?=template_admin_footer()?>
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
