document.getElementById('registerForm').addEventListener('submit', async (e) => {
    e.preventDefault();

    const name = document.getElementById('name').value;
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;

    const userData = { name, email, password };

    try {
        const response = await fetch('./server/register.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(userData),
        });

        const result = await response.json();

        if (result.success) {
            // Simpan data pengguna ke localStorage
            localStorage.setItem('user', JSON.stringify({ name, email }));
            alert('Registrasi berhasil! Anda akan diarahkan ke halaman buat event.');
            window.location.href = 'add-event.html';
        } else {
            alert('Registrasi gagal: ' + result.message);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat melakukan registrasi.');
    }
});
