<?php
include 'db.php';

// Tangkap data dari request
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Validasi input
    if (empty($email) || empty($password)) {
        echo json_encode([
            "status" => "error",
            "message" => "Email dan password wajib diisi"
        ]);
        exit;
    }

    // Query database untuk email
    $stmt = $conn->prepare("SELECT id, name, email, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verifikasi password
        if (password_verify($password, $user['password'])) {
            // Simpan data user ke session
            session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];

            echo json_encode([
                "status" => "success",
                "message" => "Login berhasil",
                "user" => [
                    "id" => $user['id'],
                    "name" => $user['name'],
                    "email" => $user['email']
                ]
            ]);
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "Password salah"
            ]);
        }
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Email tidak terdaftar"
        ]);
    }

    $stmt->close();
}

$conn->close();
?>
