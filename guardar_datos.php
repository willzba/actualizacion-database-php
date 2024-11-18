<?php
// Configuración de la base de datos
$servername = "localhost:3306"; // Cambia esto si tienes un servidor de BD distinto
$username = "root";             // Usuario de la BD
$password = "";                 // Contraseña de la BD
$dbname = "prueba_datos";       // Nombre de la BD

// Crear conexión con la base de datos
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    http_response_code(500); // Código de respuesta de error interno
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener los parámetros enviados a través de GET y sanitizar los datos
$temperature = isset($_GET['temperature']) ? intval($_GET['temperature']) : null;
$humidity = isset($_GET['humidity']) ? intval($_GET['humidity']) : null;
$ledState = isset($_GET['ledState']) ? intval($_GET['ledState']) : null;
$receiver_id = "9525"; // ID del receptor, puedes hacerlo variable si deseas recibirlo como parámetro también

// Verificar si los parámetros existen y son válidos
if ($temperature !== null && $humidity !== null && $ledState !== null) {
    // Utilizar consultas preparadas para evitar inyecciones SQL
    $stmt = $conn->prepare("INSERT INTO datos_sensor (receiver_id, temperature, humidity, ledState, fecha_hora) VALUES (?, ?, ?, ?, NOW())");
    if ($stmt) {
        // "siii" indica que el primer parámetro es string y los siguientes son enteros
        $stmt->bind_param("siii", $receiver_id, $temperature, $humidity, $ledState);

        // Ejecutar la consulta y verificar el resultado
        if ($stmt->execute()) {
            http_response_code(200); // Código de respuesta de éxito
            echo "Datos actualizados con éxito";
        } else {
            http_response_code(500); // Código de respuesta de error interno
            echo "Error al actualizar los datos: " . $stmt->error;
        }

        // Cerrar la declaración
        $stmt->close();
    } else {
        http_response_code(500); // Código de respuesta de error interno
        echo "Error al preparar la consulta: " . $conn->error;
    }
} else {
    http_response_code(400); // Código de respuesta de solicitud incorrecta
    echo "Faltan datos o son inválidos. Asegúrate de enviar todos los parámetros.";
}

// Cerrar la conexión
$conn->close();
?>
