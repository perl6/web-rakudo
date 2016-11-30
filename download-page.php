<?php
    parse_str($_SERVER['QUERY_STRING'], $_GET);
    date_default_timezone_set('UTC');
    $valid_pages = [
        'nqp' => [
            'id'   => 'nqp',
            'name' => 'NQP (Not Quite Perl) Compiler Toolkit',
            'path' => 'downloads/nqp/',
            'vars' => ['.tar.gz' => null]
        ],
        'rakudo' => [
            'id'   => 'rakudo',
            'name' => 'Rakudo Perl 6 Compiler',
            'path' => 'downloads/rakudo/',
            'vars' => ['.tar.gz' => null]
        ],
        'star' => [
            'id'   => 'rakudo-star',
            'name' => 'Rakudo Star Perl 6 Distribution',
            'path' => 'downloads/star/',
            'vars' => [
                '.tar.gz'           => null,
                '.dmg'             => null,
                '-x86_64 (JIT).msi' => null,
                '-x86 (no JIT).msi' => null
            ]
        ]
    ];

    $give_latest = array_key_exists('latest', $_GET) ? $_GET['latest'] : null;
    $asset = array_key_exists('asset', $_GET) ? (
        array_key_exists($_GET['asset'], $valid_pages)
            ? $valid_pages[ $_GET['asset'] ] : null
    ) : null;
    if ( ! $asset ) { echo 'Failure'; exit; }

    function human_filesize($bytes) {
        if ($bytes == 0)
            return "0.00 B";

        $s = array(' B', ' KB', ' MB', ' GB', ' TB', ' PB');
        $e = floor(log($bytes, 1024));

        return round($bytes/pow(1024, $e), 2).$s[$e];
    }
    function make_sig($file) {
        $sig = $file . '.asc';
        if ( ! file_exists($sig) ) { return ''; }
        return ' <a href="/' . $sig . '" class="sig">PGP</a>';
    }

    $files = array_reverse(glob(
        $asset['path'] . '*.{msi,dmg,tar.gz,zip}',
        GLOB_BRACE
    ));
    foreach (array_keys($asset['vars']) as $variant) {
        foreach ($files as $file) {
            if ( substr($file, -strlen($variant)) !== $variant ) {
                continue;
            }
            $asset['vars'][$variant] = substr(
                substr($file, strlen($asset['path'])),
                0, -strlen($variant)
            );
            break;
        }
    }

    if ( $give_latest
        && array_key_exists($give_latest, $asset['vars'])
        && $asset['vars'][$give_latest]
    ) {
        header("HTTP/1.1 303 See Other");
        header( "Location: /" . $asset['path']
            . $asset['vars'][$give_latest] . $give_latest
        );
        exit();
    }
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta property="og:image"
    content="http://rakudo.org/wp/wp-content/uploads/2011/04/rakudo-1001.png">
    <title>Downloads - <?php echo $asset['name']; ?></title>
    <link href="/download-page.css" rel="stylesheet">
  </head>
  <body>

<div id="files-container">
    <h1>
        Download <?php echo $asset['name']; ?>
        <span class="logo-<?php echo $asset['id']; ?>"></span>
    </h1>
    <p class="back-link"><a href="/">back to home page</a></p>

    <div id="latest-container">
        <h2>Latest Release</h2>
        <table id="latest">
        <thead>
            <tr>
                <th>File</th>
                <th>Size</th>
            </tr>
        </thead>
        <tbody>
        <?php
            foreach (array_keys($asset['vars']) as $variant) {
                if ( ! $asset['vars'][$variant] ) {
                    continue;
                }
                $human_name = $asset['vars'][$variant] . $variant;
                $filename = $asset['path'] . $human_name;
                echo "<tr>";
                echo "<td class='file'><a href='/$filename' ";
                echo " class='ext-" . pathinfo($filename, PATHINFO_EXTENSION);
                echo "'>$human_name</a>" . make_sig($filename) . "</td>";
                echo "<td>" . human_filesize(filesize($filename)) . "</td>";
                echo "</tr>\n";
            }
        ?>
        </tbody>
        </table>
    </div>

    <h2>All Releases</h2>
    <table id="files">
    <thead>
        <tr>
            <th>File</th>
            <th>Last Modified</th>
            <th>Size</th>
        </tr>
    </thead>
    <tbody>
    <?php
        $prefix_len = strlen($asset['path']);
        foreach ($files as $file) {
            $human_name = substr($file, $prefix_len);
            echo "<tr>";
            echo "<td class='file'><a href='/$file' ";
            echo " class='ext-" . pathinfo($file, PATHINFO_EXTENSION);
            echo "'>$human_name</a>" . make_sig($filename) . "</td>";
            echo "<td>" . strftime('%F %R', filemtime($file)) . "</td>";
            echo "<td>" . human_filesize(filesize($file)) . "</td>";
            echo "</tr>\n";
        }
    ?>
    </tbody>
    </table>

    <div id="perma-latest">
        <p>Permalink that always gives the latest available release:</p>
        <ul>
        <?php
            foreach (array_keys($asset['vars']) as $variant) {
                if ( ! $asset['vars'][$variant] ) { continue; }
                $filename = $asset['id'] . '-latest' . $variant;
                echo '<li><a href="/' . $asset['path'] . $filename . '">'
                    . $filename . '</a></li>';
            }
        ?>
        </ul>
    </div>
</div>
  </body>
</html>
