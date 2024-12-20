<?php
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}include('conf.php'); 

if (isset($_SESSION['admin']) && ($_SESSION['admin'] == 1)) {
    echo "<div align=center><h5>You are already logged in</h5></div>";
} else {
    if (isset($_POST['submit'])) {
        $username = mysqli_real_escape_string($conx, $_POST['username']);
        $password = mysqli_real_escape_string($conx, $_POST['password']);
        
        $stmt = $conx->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $creds = $result->fetch_array(MYSQLI_ASSOC);

        if ($creds && password_verify($password, $creds['password'])) {
            $_SESSION['username'] = $creds['username'];
            $_SESSION['admin'] = 1;
            header("Location: ".$_SERVER['PHP_SELF']);
        } else {
            echo "<div align=center><h5>Invalid username or password</h5></div>";
        }
    } else {
?>
<div align=center>
    <fieldset style=width:300;>
        <legend><b>Login</b></legend>
        <form action="<?php echo $_SERVER['PHP_SELF']."?action=login"; ?>" method="post">
            <br>Username: <input type="text" name="username" required><br>
            Password: <input type="password" name="password" required><br>
            <br><input type="submit" name="submit" value="Login"><br>
        </form>
    </fieldset>
</div>
<?php
    }
}
?>
