<?php
// Valores por defecto para el formulario
$data = array(
    'email'      => 'email',
    'firstname'  => 'nombre',
    'lastname'   => 'apellidos',
    'postcode'   => 'codigo postal',
    'city'       => 'ciudad',
    'stateProv'  => 'provincia',
    'country'    => 'pais',
    'telephone'  => 'telefono',
    'password'   => 'contraseña',
    'password2'  => 'repetir contraseña',
    'imagen'     => NULL // Inicializado como NULL
);

$error = array(
    'email'      => '',
    'firstname'  => '',
    'lastname'   => '',
    'city'       => '',
    'stateProv'  => '',
    'country'    => '',
    'postcode'   => '',
    'telephone'  => '',
    'password'   => '',
    'imagen'     => ''
);

if (isset($_POST['data'])) {
    $data = $_POST['data'];

    if ($data['password'] !== $data['password2']) {
        $error['password'] = 'Las contraseñas no coinciden.';
    }

    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $path = "perfiles/" . basename($_FILES['imagen']['name']);
        if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $path)) {
            $error['imagen'] = 'Error al cargar la imagen.';
        } else {
            $data['imagen'] = basename($_FILES['imagen']['name']);
        }
    }

    if (empty(array_filter($error))) {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        include('conf.php'); 

        $stmt = mysqli_prepare($conx, "INSERT INTO users (username, password, izena, abizena, hiria, lurraldea, herrialdea, postakodea, telefonoa, irudia) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $password_hashed = password_hash($data['password'], PASSWORD_DEFAULT); 

        mysqli_stmt_bind_param(
            $stmt, 
            "ssssssssss", 
            $data['email'], 
            $password_hashed, 
            $data['firstname'], 
            $data['lastname'], 
            $data['city'], 
            $data['stateProv'], 
            $data['country'], 
            $data['postcode'], 
            $data['telephone'], 
            $data['imagen'] 
        );

        if (mysqli_stmt_execute($stmt)) {
            header("Location: index.php"); 
            exit();
        } else {
            die('Error: ' . mysqli_error($conx));
        }

        mysqli_stmt_close($stmt);
        mysqli_close($conx);
    }
}
?>

<div class="content">
    <br/>
    <div class="register">
        <h2>Erregistroa egin</h2>
        <br/>
        <b>Introduce la información.</b>
        <br/>
        <form action="<?php echo $_SERVER['PHP_SELF'] . "?action=register"; ?>" method="POST" enctype="multipart/form-data">
            <p>
                <label>Email/username: </label>
                <input type="text" name="data[email]" value="<?php echo $data['email']; ?>" />
                <?php if ($error['email']) echo '<p>', $error['email']; ?>
            </p>
            <p>
                <label>Izena: </label>
                <input type="text" name="data[firstname]" value="<?php echo $data['firstname']; ?>" />
                <?php if ($error['firstname']) echo '<p>', $error['firstname']; ?>
            </p>
            <p>
                <label>Abizena: </label>
                <input type="text" name="data[lastname]" value="<?php echo $data['lastname']; ?>" />
                <?php if ($error['lastname']) echo '<p>', $error['lastname']; ?>
            </p>
            <p>
                <label>Hiria: </label>
                <input type="text" name="data[city]" value="<?php echo $data['city']; ?>" />
                <?php if ($error['city']) echo '<p>', $error['city']; ?>
            </p>
            <p>
                <label>Lurraldea: </label>
                <input type="text" name="data[stateProv]" value="<?php echo $data['stateProv']; ?>" />
                <?php if ($error['stateProv']) echo '<p>', $error['stateProv']; ?>
            </p>
            <p>
                <label>Herrialdea: </label>
                <input type="text" name="data[country]" value="<?php echo $data['country']; ?>" />
                <?php if ($error['country']) echo '<p>', $error['country']; ?>
            </p>
            <p>
                <label>Postakodea: </label>
                <input type="text" name="data[postcode]" value="<?php echo $data['postcode']; ?>" />
                <?php if ($error['postcode']) echo '<p>', $error['postcode']; ?>
            </p>
            <p>
                <label>Telefonoa: </label>
                <input type="text" name="data[telephone]" value="<?php echo $data['telephone']; ?>" />
                <?php if ($error['telephone']) echo '<p>', $error['telephone']; ?>
            </p>
            <p>
                <label>Pasahitza: </label>
                <input type="password" name="data[password]" value="<?php echo $data['password']; ?>" />
                <?php if ($error['password']) echo '<p>', $error['password']; ?>
            </p>
            <p>
                <label>Pasahitza errepikatu: </label>
                <input type="password" name="data[password2]" value="<?php echo $data['password2']; ?>" />
            </p>
            <p>
                <label>Irudia aukeratu (aukerakoa):</label>
                <input name="imagen" type="file" />
                <?php if ($error['imagen']) echo '<p>', $error['imagen']; ?>
            </p>
            <p>
                <input type="reset" name="data[clear]" value="Clear" class="button"/>
                <input type="submit" name="data[submit]" value="Submit" class="button marL10"/>
            </p>
        </form>
    </div>
</div>
