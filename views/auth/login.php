<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sport Portal</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://accounts.google.com/gsi/client" async defer></script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&display=swap');

        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="text-slate-800">

    <?php if (isset($_GET['err'])): ?>
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: '<?= htmlspecialchars($_GET['err']) ?>',
                confirmButtonColor: '#1e3a8a'
            });
        </script>
    <?php endif; ?>
    <?php if (isset($_GET['success'])): ?>
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: '<?= htmlspecialchars($_GET['success']) ?>',
                confirmButtonColor: '#1e3a8a'
            });
        </script>
    <?php endif; ?>

    <div class="min-h-screen flex items-center justify-center p-4 bg-gradient-to-br from-slate-900 to-blue-900">
        <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl overflow-hidden p-8 fade-in">

            <div class="text-center mb-6">
                <h1 class="text-3xl font-black text-blue-900">SPORT<span class="text-slate-800">PORTAL</span></h1>
                <p class="text-slate-500 text-sm">
                    <?= isset($_GET['type']) && $_GET['type'] == 'register' ? 'Daftar akun baru' : 'Masuk ke akun Anda' ?>
                </p>
            </div>

            <?php if (!isset($_GET['type']) || $_GET['type'] != 'register'): ?>
                <div class="mb-6">
                    <div id="g_id_onload"
                        data-client_id="<?= GOOGLE_CLIENT_ID ?>"
                        data-context="signin"
                        data-ux_mode="popup"
                        data-callback="handleCredentialResponse"
                        data-auto_prompt="false">
                    </div>

                    <div class="w-full flex justify-center">
                        <div class="g_id_signin"
                            data-type="standard"
                            data-shape="pill"
                            data-theme="outline"
                            data-text="signin_with"
                            data-size="large"
                            data-logo_alignment="left"
                            data-width="400"> </div>
                    </div>

                    <div class="relative flex py-5 items-center">
                        <div class="flex-grow border-t border-gray-200"></div>
                        <span class="flex-shrink-0 mx-4 text-gray-400 text-xs">ATAU</span>
                        <div class="flex-grow border-t border-gray-200"></div>
                    </div>
                </div>

                <script>
                    function handleCredentialResponse(response) {
                        // Membuat form virtual untuk mengirim token Google ke backend
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = 'backend/process.php'; // Pastikan path ini benar sesuai struktur folder

                        // Input Action
                        const actInput = document.createElement('input');
                        actInput.type = 'hidden';
                        actInput.name = 'act';
                        actInput.value = 'google_login';
                        form.appendChild(actInput);

                        // Input Token
                        const credInput = document.createElement('input');
                        credInput.type = 'hidden';
                        credInput.name = 'credential';
                        credInput.value = response.credential;
                        form.appendChild(credInput);

                        document.body.appendChild(form);
                        form.submit();
                    }
                </script>
            <?php endif; ?>

            <form action="backend/process.php" method="POST" class="space-y-4">

                <?php if (isset($_GET['type']) && $_GET['type'] == 'register'): ?>
                    <input type="hidden" name="act" value="register">
                    <input type="text" name="name" class="w-full p-3 rounded-lg border bg-slate-50 focus:ring-2 focus:ring-blue-900 focus:outline-none transition" placeholder="Nama Lengkap" required>
                <?php else: ?>
                    <input type="hidden" name="act" value="login">
                <?php endif; ?>

                <input type="email" name="email" class="w-full p-3 rounded-lg border bg-slate-50 focus:ring-2 focus:ring-blue-900 focus:outline-none transition" placeholder="Email" required>
                <input type="password" name="password" class="w-full p-3 rounded-lg border bg-slate-50 focus:ring-2 focus:ring-blue-900 focus:outline-none transition" placeholder="Password" required>

                <button class="w-full bg-slate-800 text-white font-bold py-3 rounded-lg hover:bg-blue-900 transition duration-300 shadow-md">
                    <?= isset($_GET['type']) && $_GET['type'] == 'register' ? 'Daftar' : 'Masuk' ?>
                </button>
            </form>

            <div class="mt-6 text-center text-sm space-y-2">
                <a href="?p=dashboard" class="block text-slate-400 font-medium hover:text-blue-900 transition">
                    ← Masuk sebagai Guest
                </a>

                <a href="?p=auth/login<?= isset($_GET['type']) && $_GET['type'] == 'register' ? '' : '&type=register' ?>" class="block text-blue-900 font-bold hover:underline">
                    <?= isset($_GET['type']) && $_GET['type'] == 'register' ? 'Sudah punya akun? Login' : 'Belum punya akun? Daftar' ?>
                </a>
            </div>

        </div>
    </div>

    <script>
        gsap.from(".fade-in", {
            duration: 0.8,
            y: 30,
            opacity: 0,
            ease: "power2.out"
        });
    </script>
</body>

</html>