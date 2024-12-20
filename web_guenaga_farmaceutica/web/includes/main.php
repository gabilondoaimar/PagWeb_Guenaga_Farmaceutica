<?php

if (!$conx) {
    die("Errorea datu basearekin konexioan.");
}

$search = "";
$testua = "";

if (isset($_GET['keyword']) && $_GET['keyword'] != '') {
    $testua = htmlspecialchars($_GET['keyword'], ENT_QUOTES, 'UTF-8'); 
}

$search = "%" . $testua . "%"; 

if ($stmt = $conx->prepare("SELECT * FROM produktuak WHERE izena LIKE ? OR deskripzioa LIKE ? LIMIT 50")) {
    $stmt->bind_param("ss", $search, $search);
    $stmt->execute();
    $result = $stmt->get_result();

    $produktuak = [];
    while ($row = $result->fetch_assoc()) {
        $produktuak[] = $row;
    }
    $stmt->close();
} else {
    error_log("Errorea kontsulta prestatzerakoan: " . $conx->error);
    die("Errorea kontsulta burutzean.");
}

if (sizeof($produktuak) == 0) {
    echo "<fieldset style='width:500;'>";
    echo "<legend><b>Ez dago produkturik katalogoan " . htmlspecialchars($testua, ENT_QUOTES, 'UTF-8') . " deitzen denik</b></legend>";
    if (isset($_SESSION['admin'])) {
        echo "<div align='center'><h3><b><a href='" . htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . "?action=updel'>Eguneratu</a> katalogoa.</b></h3></div>";
    } else {
        echo "<div align='center'><h3>Enpresaren hasierako orria</h3></div>";
    }
    echo "</fieldset>";
} else {
    ?>
    <table width="1000" cellpadding="10" cellspacing="10" align="center">
    <?php foreach ($produktuak as $data) { ?>
        <tr>
            <td align="center" valign="top" width="40%">
                <fieldset>
                <br>
                <a href="<?php echo "images/" . htmlspecialchars($data["pic"], ENT_QUOTES, 'UTF-8'); ?>">
                    <img src="images/<?php echo htmlspecialchars($data["pic"], ENT_QUOTES, 'UTF-8'); ?>" border="1">
                </a>
                <br><br>
                </fieldset>
            </td>
            <td valign="top" width="60%">
                <fieldset>
                    <legend><b>Izena</b></legend>
                    <br>
                    <?php echo htmlspecialchars($data['izena'], ENT_QUOTES, 'UTF-8') . " - " . htmlspecialchars($data['salneurria'], ENT_QUOTES, 'UTF-8') . "€"; ?>
                    <br>
                </fieldset>
                <fieldset>
                    <legend><b>Deskripzioa</b></legend>
                    <br>
                    <?php echo htmlspecialchars($data['deskripzioa'], ENT_QUOTES, 'UTF-8'); ?>
                    <br>
                </fieldset>
                <br>
                <?php
                if (isset($_SESSION['admin']) && ($_SESSION['admin'] == 1)) {
                    if ($_SESSION['username'] == 'admin@bdweb') {
                        ?>
                        <table width="100%" cellpadding="2" cellspacing="2" align="center">
                        <tr><td width="50%" align="left">
                        <a href="<?php echo htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . "?action=description&pic_id=" . htmlspecialchars($data['id'], ENT_QUOTES, 'UTF-8'); ?>">
                            <b>Deskripzioa/Salneurria aldatu</b>
                        </a>
                        </td></tr>
                        </table>
                        <?php
                    } else {
                        echo "<a href='#'><img src='images/generikoa1.png'></a>";
                    }
                }
                ?>
            </td>
        </tr>
    <?php } ?>
    </table>
    <?php
}
?>
