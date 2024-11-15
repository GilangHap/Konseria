document.addEventListener('DOMContentLoaded', () => {
    const urlParams = new URLSearchParams(window.location.search);
    const eventId = urlParams.get('id');

    if (!eventId) {
        console.error("Event ID tidak ditemukan di URL.");
        document.querySelector('.event-detail-container').innerHTML = `
            <div class="alert alert-danger">Event tidak ditemukan. Silakan kembali ke halaman utama.</div>
        `;
        return;
    }

    // Fetch data event dari backend PHP
    fetch(`./server/detail.php?id=${eventId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const event = data.event;

                // Isi detail event ke elemen HTML
                document.getElementById('eventTitle').innerText = event.title;
                document.getElementById('eventMainImage').src = event.imageURL;
                document.getElementById('organizerLogo').src = event.organizerImage;
                document.getElementById('organizerName').innerText = event.organizer;
                document.getElementById('eventDate').innerText = event.date;
                document.getElementById('eventTime').innerText = event.time;
                document.getElementById('eventLocation').innerText = event.location;
                document.getElementById('eventDescription').innerText = event.description;
                document.getElementById('ticketPrice').innerText = `Harga Tiket: Rp ${parseInt(event.ticketPrice).toLocaleString()}`;
                
                // Validasi ketersediaan tiket
                if (event.availableTickets > 0) {
                    const availabilityInfo = document.createElement('small');
                    availabilityInfo.classList.add('text-muted', 'd-block');
                    availabilityInfo.innerText = `Tersedia: ${event.availableTickets} tiket`;
                    document.getElementById('buyTicketBtn').parentNode.insertBefore(availabilityInfo, document.getElementById('buyTicketBtn').nextSibling);
                } else {
                    const buyTicketBtn = document.getElementById('buyTicketBtn');
                    buyTicketBtn.disabled = true;
                    buyTicketBtn.innerText = "Tiket Habis";
                    buyTicketBtn.classList.add('btn-secondary');
                }

                // Event listener untuk tombol beli tiket
                document.getElementById('buyTicketBtn').addEventListener('click', () => {
                    addToCart(event);
                });
            } else {
                console.error(data.message);
                document.querySelector('.event-detail-container').innerHTML = `
                    <div class="alert alert-danger">Event tidak ditemukan. Silakan kembali ke halaman utama.</div>
                `;
            }
        })
        .catch(error => {
            console.error("Error fetching event:", error);
            document.querySelector('.event-detail-container').innerHTML = `
                <div class="alert alert-danger">Terjadi kesalahan saat mengambil data event.</div>
            `;
        });
});

// Fungsi untuk menambahkan event ke keranjang
function addToCart(event) {
    // Ambil keranjang dari localStorage
    let cartItems = JSON.parse(localStorage.getItem('cartItems')) || [];

    // Cek apakah event sudah ada di keranjang
    const existingItemIndex = cartItems.findIndex(item => item.id === event.id);

    if (existingItemIndex > -1) {
        // Jika sudah ada, tambahkan kuantitas
        cartItems[existingItemIndex].quantity += 1;
    } else {
        // Jika belum ada, tambahkan event baru
        cartItems.push({
            id: event.id,
            title: event.title,
            ticketPrice: parseInt(event.ticketPrice),
            imageURL: event.imageURL,
            date: event.date,
            location: event.location,
            quantity: 1
        });
    }

    // Simpan kembali ke localStorage
    localStorage.setItem('cartItems', JSON.stringify(cartItems));

    // Redirect ke halaman keranjang
    window.location.href = 'keranjang.html';
}
