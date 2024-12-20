<?php
include('conf.php');

if (isset($_GET['pic_id'])) {
    $pic_id = $_GET['pic_id'];

    if ($conx) {
        $stmt = $conx->prepare("SELECT deskripzioa, salneurria FROM produktuak WHERE ID = ?");
        
        if ($stmt === false) {
            die("Error en la preparación de la consulta: " . $conx->error);
        }

        $stmt->bind_param("i", $pic_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $data = $result->fetch_assoc();
            
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $deskripzioa = $_POST['deskripzioa'];
                $salneurria = $_POST['salneurria'];

                $update_stmt = $conx->prepare("UPDATE produktuak SET deskripzioa = ?, salneurria = ? WHERE ID = ?");
                $update_stmt->bind_param("ssi", $deskripzioa, $salneurria, $pic_id);
                if ($update_stmt->execute()) {
                    echo "<p>Descripción y precio actualizados correctamente.</p>";
                } else {
                    echo "<p>Error al actualizar los datos.</p>";
                }
                $update_stmt->close();
            }

            echo "<h4>Edita el producto</h4>";
            echo '<form method="POST">';
            echo '<label for="deskripzioa">Descripción:</label><br>';
            echo '<textarea name="deskripzioa" id="deskripzioa" rows="4" cols="50">' . htmlspecialchars($data['deskripzioa'], ENT_QUOTES, 'UTF-8') . '</textarea><br>';
            echo '<label for="salneurria">Precio:</label><br>';
            echo '<input type="text" name="salneurria" id="salneurria" value="' . htmlspecialchars($data['salneurria'], ENT_QUOTES, 'UTF-8') . '"><br>';
            echo '<input type="submit" value="Eguneratu">';
            echo '</form>';
        } else {
            echo "No se encontró el producto con ese ID.";
        }

        $stmt->close();
    } else {
        echo "Error en la conexión a la base de datos.";
    }
} else {
    echo "El ID del producto no está definido.";
}
?>
