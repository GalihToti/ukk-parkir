<?php
session_start();
// Jika sudah login, langsung lempar ke dashboard sesuai role
if (isset($_SESSION['role'])) {
    header("Location:../" . $_SESSION['role'] . "/index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - E-Parkir System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="bg-[#fafafa] flex items-center justify-center min-h-screen p-4">

    <div class="w-full max-w-[400px] space-y-6">

        <div class="text-center space-y-2">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-green-600 text-white shadow-lg shadow-green-200 mb-2">
                <i class="fas fa-parking text-2xl"></i>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-gray-900">Selamat Datang</h1>
            <p class="text-sm text-gray-500">Silakan masukkan akun untuk akses e-parkir.</p>
        </div>

        <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-200">

            <?php if (isset($_SESSION['error'])) : ?>
                <div class="bg-red-50 border border-red-100 text-red-600 px-4 py-3 rounded-lg flex items-center gap-3 mb-6 transition-all animate-pulse">
                    <i class="fas fa-exclamation-circle"></i>
                    <span class="text-xs font-semibold"><?= $_SESSION['error']; ?></span>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <form action="proseslogin.php" method="POST" class="space-y-5">
                <div class="space-y-2">
                    <label for="username" class="text-[11px] font-bold uppercase tracking-wider text-gray-500 ml-1">Username</label>
                    <div class="relative">
                        <i class="fas fa-user absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                        <input type="text" id="username" name="username"
                            class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-green-500 focus:border-transparent outline-none transition-all text-sm text-gray-900 placeholder-gray-400"
                            placeholder="Masukkan username anda" required autofocus>
                    </div>
                </div>

                <div class="space-y-2">
                    <label for="password" class="text-[11px] font-bold uppercase tracking-wider text-gray-500 ml-1">Password</label>
                    <div class="relative">
                        <i class="fas fa-lock absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                        <input type="password" id="password" name="password"
                            class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-green-500 focus:border-transparent outline-none transition-all text-sm text-gray-900 placeholder-gray-400"
                            placeholder="••••••••" required>
                    </div>
                </div>

                <button type="submit" name="login" class="w-full bg-gray-900 hover:bg-green-600 text-white font-bold py-3 rounded-xl shadow-sm hover:shadow-lg hover:shadow-green-100 transition-all duration-300 flex items-center justify-center gap-2 mt-2">
                    <span>Masuk</span>
                    <i class="fas fa-arrow-right text-xs"></i>
                </button>
            </form>
        </div>

        <p class="text-center text-xs text-gray-400 font-medium">
            &copy; 2026 E-Parkir System — Galih Toti XII RPL 1
        </p>
    </div>

</body>

</html>