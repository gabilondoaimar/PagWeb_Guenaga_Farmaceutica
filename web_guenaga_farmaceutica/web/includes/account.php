<?php
if (session_status() == PHP_SESSION_NONE) {
  session_start(); // Solo iniciar sesión si no está ya activa
}include('conf.php'); // Conexión a la base de datos

if (!isset($_SESSION['admin'])) {
    header("Location: ".$_SERVER['PHP_SELF']);
}

if (isset($_GET['changepass'])) {
    // Sanitización de los datos de entrada
    $newpass = mysqli_real_escape_string($conx, $_POST['newpass']);
    $confnewpass = mysqli_real_escape_string($conx, $_POST['confnewpass']);
    
    // Validación de contraseñas
    if ($newpass != $confnewpass) {
        header("Location: ".$_SERVER['PHP_SELF']."?action=account");
    } else {
        // Hash de la nueva contraseña
        $hashed_newpass = password_hash($newpass, PASSWORD_DEFAULT);
        
        $oldpass = $_SESSION['password'];
        $stmt = $conx->prepare("UPDATE users SET password = ? WHERE password = ?");
        $stmt->bind_param("ss", $hashed_newpass, $oldpass);
        $stmt->execute();
        
        session_destroy();
        header("Location: ".$_SERVER['PHP_SELF']);
    }
} elseif(isset($_GET['changephoto'])) {
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        // Fitxategiaren izena eta bidea
        $path = "perfiles/" . basename($_FILES['imagen']['name']);
        // Fitxategia mugitu eta eguneratu datu basean
        if (move_uploaded_file($_FILES['imagen']['tmp_name'], $path)) {
            $stmt = $conx->prepare("UPDATE users SET irudia = ? WHERE username = ?");
            $stmt->bind_param("ss", $_FILES['imagen']['name'], $_SESSION['username']);
            $stmt->execute();
            header("Location: ".$_SERVER['PHP_SELF']."?action=account");
        } else {
            echo "<p>Error al cargar la imagen.</p>";
        }
    } else {
        echo "<p>No se seleccionó ninguna imagen o hubo un error al subirla.</p>";
    }
}elseif (isset($_GET['adduser']) && $_SESSION['username'] == 'admin') {
    $newuser = mysqli_real_escape_string($conx, $_POST['newuser']);
    $newuserpass = mysqli_real_escape_string($conx, $_POST['newuserpass']);
    
    $hashed_newuserpass = password_hash($newuserpass, PASSWORD_DEFAULT);
    
    $stmt = $conx->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    $stmt->bind_param("ss", $newuser, $hashed_newuserpass);
    $stmt->execute();
    
    header("Location: ".$_SERVER['PHP_SELF']."?action=account");
} elseif (isset($_GET['deleteuser']) && $_SESSION['username'] == 'admin') {
    if ($_GET['deleteuser'] == $_SESSION['username']) {
        header("Location: ".$_SERVER['PHP_SELF']."?action=account");
    } else {
        $username_to_delete = mysqli_real_escape_string($conx, $_GET['deleteuser']);
        $stmt = $conx->prepare("DELETE FROM users WHERE username = ?");
        $stmt->bind_param("s", $username_to_delete);
        $stmt->execute();
        
        header("Location: ".$_SERVER['PHP_SELF']."?action=account");
    }
} else {
?>

<div align=center>
    <table width=1000 cellpadding=10 cellspacing=10>
        <tr>
            <td valign=top align=right>
                <fieldset style=width:300;>
                    <legend><b>Change Password</b></legend>
                    <form action="<?php echo $_SERVER['PHP_SELF']."?action=account&changepass=1"; ?>" method="POST">
                        New Password: <input type="password" name="newpass" required><br>
                        Confirm New Password: <input type="password" name="confnewpass" required><br><br>
                        <input type="submit" value="Change">
                    </form>
                </fieldset>
            </td>
            
            <?php if ($_SESSION['username'] == 'admin@bdweb') { ?>
            <td valign=top align=left>
                <fieldset style=width:300;>
                    <legend><b>Add User</b></legend>
                    <form action="<?php echo $_SERVER['PHP_SELF']."?action=account&adduser=1"; ?>" method="POST">
                        New user's username: <input type="text" name="newuser" required><br>
                        New user's password: <input type="text" name="newuserpass" required><br><br>
                        <input type="submit" value="Add">
                    </form>
                </fieldset><br>
            </tr>
            <tr>
            <td valign=top align=right>
                  <fieldset style=width:300;>
                      <legend><b>Change Profile Photo</b></legend>
                         <form action="<?php echo $_SERVER['PHP_SELF']."?action=account&changephoto=1"; ?>" method="POST" enctype="multipart/form-data">
                            Select new photo: <input type="file" name="imagen" required><br><br>
                            <input type="submit" value="Change Photo">
                          </form>
                </fieldset>
            </td>
            <td>
                <fieldset style=width:300;>
                    <legend><b>Delete User</b></legend>
                    <table cellpadding=2 cellspacing=2 width=100%>
                         
                        <?php
                            $users = mysqli_query($conx, "SELECT username FROM users");
                            while ($user = mysqli_fetch_array($users)) {
                                echo "<tr>";
                                echo "<td align=left class=box>";
                                if ($user['username'] == $_SESSION['username']) {
                                    echo "<b>".$user['username']."</b>";
                                } else {
                                    echo $user['username'];
                                }
                                echo "</td>";
                                echo "<td align=right class=box width=60>";
                                if ($user['username'] == $_SESSION['username']) {
                                    echo "<del>[delete]</del>&nbsp;";
                                } else {
                                    echo "<a href=".$_SERVER['PHP_SELF']."?action=account&deleteuser=".$user['username'].">[delete]</a>&nbsp;";
                                }
                                echo "</td>";
                                echo "</tr>";
                            }
                        ?>
                    </table>
                </fieldset>
            </td>
            <?php } ?>
        </tr>
    </table>
</div>

<?php
}
?>
