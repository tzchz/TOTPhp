<?php
function base32_decode($b32) {
    $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
    $out = '';
    $l = strlen($b32);
    $n = 0;
    $j = 0;

    for ($i = 0; $i < $l; $i++) {
        $n = $n << 5;
        $n = $n + strpos($alphabet, $b32[$i]);
        $j = $j + 5;

        if ($j >= 8) {
            $j = $j - 8;
            $out .= chr(($n & (0xFF << $j)) >> $j);
        }
    }
    return $out;
}

function get_totp_code($key) {
    $key = base32_decode($key);
    $time = floor(time() / 30);
    $time = pack('N*', 0) . pack('N*', $time);
    $hm = hash_hmac('sha1', $time, $key, true);
    $offset = ord($hm[19]) & 0xf;
    $code = ((
        (ord($hm[$offset + 0]) & 0x7f) << 24 |
        (ord($hm[$offset + 1]) & 0xff) << 16 |
        (ord($hm[$offset + 2]) & 0xff) << 8 |
        (ord($hm[$offset + 3]) & 0xff)
    ) % 1000000);
    return str_pad($code, 6, '0', STR_PAD_LEFT);
}

if($_GET['id'] and $_GET['key']){
    $pattern = '/^[a-zA-Z0-9@._-]+$/';
    if(!preg_match($pattern, $_GET['id']))die('<script>alert("Check your Account ID");location = ".";</script>');
    
    $pattern = '/^[A-Z2-7]+$/i';
    if(!preg_match($pattern, $_GET['key']))die('<script>alert("Check your 2FA Key");location = ".";</script>');
    
    $db = new SQLite3('sqlite.db');
    
    $db->exec("INSERT INTO tab0 (id, key) VALUES ('".$_GET['id']."', '".$_GET['key']."')");
    
    $db->close();
    
    die('<script>alert("Success");location = ".";</script>');

}

$id = isset($_GET['id']) ? $_GET['id'] : '';
if ($id) {
    $db = new SQLite3('sqlite.db');
    $results = $db->query("SELECT * FROM tab0 WHERE id = '$id'");

    while ($row = $results->fetchArray()) {
        $key = $row['key'];
    }

    $db->close();

    echo '<script>
    window.onload = function() {
        navigator.clipboard.writeText("' . get_totp_code($key) . '").then(function() {
            alert("TOTP ' . get_totp_code($key) . ' Copied");
            location = ".";
        }).catch(function(error) {
            alert("Clipboard Permission Denied");
            location = ".";
        });
    }
</script>';


} else {
    $db = new SQLite3('sqlite.db');

    $results = $db->query('SELECT * FROM tab0');

?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>TOTPhp</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="https://assets.060418.best/favicon.ico">
    <link rel="manifest" href="manifest.json">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script>
      window.addEventListener('beforeinstallprompt', event => {
          event.userChoice.then(result => {console.log(result.outcome)})
        }
      )
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <script src="app.js"></script>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f0f0f0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        ul {
            list-style-type: none;
            padding: 0;
        }
        li {
            margin: 10px 0;
        }
        button {
            padding: 10px 20px;
            border: none;
            background-color: #4CAF50;
            color: white;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
            width: 250px;
            text-align: left;
        }
        button:hover {
            background-color: #45a049;
        }
        .add-button {
            background-color: #008CBA;
            width: auto;
        }
        .add-button:hover {
            background-color: #007bb5;
        }
        #top-right-corner {
            position: fixed;
            top: 0;
            right: 0;
        }
        .icon {
            margin-right: 10px;
        }
        .title {
            font-size: 24px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .title i {
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <ul>
        <li><div class="title">
            <i class="fas fa-key"></i>
            <span>TOTPhp</span>
        </div>
        </li>
        <?php
        while ($row = $results->fetchArray()) {
            echo '<li><button onclick="window.location.replace(&apos;?id=' . $row['id'] . '&apos;)"><i class="fas fa-id-badge icon"></i>' . $row['id'] . '</button></li>';
        }
        ?>
        <div id="top-right-corner">
            <a href="https://github.com/tzchz/TOTPhp">
                <img decoding="async" width="149" height="149" src="https://github.blog/wp-content/uploads/2008/12/forkme_right_darkblue_121621.png" class="attachment-full size-full" alt="Fork me on GitHub" loading="lazy">
            </a>
        </div>
        <li><button class="add-button" onclick="var id=prompt('Enter Account ID');var key=prompt('Enter 2FA Key');key=key.replace(/\s/g, '').toUpperCase();window.location.replace('?key='+key+'&id='+id);"><i class="fas fa-plus icon"></i>Add</button></li>
    </ul>
</body>
</html><?
    $db->close();
}