document.addEventListener('DOMContentLoaded', () => {
    const eventList = document.getElementById('eventList');
    const searchInput = document.getElementById('eventSearch');
    const searchBtn = document.getElementById('searchBtn');
    const carouselContainer = document.querySelector('.carousel-container');
    const slides = document.querySelectorAll('.carousel-slide');

    // Carousel Functionality
    let currentIndex = 0;

    function updateCarousel() {
        const offset = -currentIndex * 100;
        carouselContainer.style.transform = `translateX(${offset}%)`;
    }

    function nextSlide() {
        currentIndex++;
        if (currentIndex >= slides.length) {
            currentIndex = 0;
        }
        updateCarousel();
    }

    function prevSlide() {
        currentIndex--;
        if (currentIndex < 0) {
            currentIndex = slides.length - 1;
        }
        updateCarousel();
    }

    // Cek apakah elemen kontrol carousel ada sebelum menambahkan event listener
    const nextButton = document.querySelector('.carousel-next');
    const prevButton = document.querySelector('.carousel-prev');

    if (nextButton) {
        nextButton.addEventListener('click', nextSlide);
    }

    if (prevButton) {
        prevButton.addEventListener('click', prevSlide);
    }

    // Geser otomatis setiap 3 detik
    setInterval(nextSlide, 3000);

    // Fungsi untuk menampilkan event
    function displayEvents(events) {
        eventList.innerHTML = ''; // Bersihkan daftar event sebelumnya
        
        if (events.length === 0) {
            eventList.innerHTML = '<p>Tidak ada event ditemukan.</p>';
            return;
        }

        events.forEach(event => {
            const eventCard = document.createElement('div');
            eventCard.classList.add('event-card');
            
            // Pastikan semua properti event tersedia
            eventCard.innerHTML = `
                <img src="${event.imageURL || '/images/default-image.jpg'}" alt="${event.title || 'Event'}">
                <h3>${event.title || 'Judul Tidak Tersedia'}</h3>
                <p>📅 ${event.date || 'Tanggal Tidak Tersedia'}</p>
                <p>📍 ${event.location || 'Lokasi Tidak Tersedia'}</p>
                <p>💰 Rp ${event.ticketPrice || 'Harga Tidak tersedia'}</p>
                <a href="detail.html?id=${event.id}"><button>Beli Tiket</button></a>
            `;
            eventList.appendChild(eventCard);
        });
    }

    // Ambil event dari server
    function fetchEvents() {
        eventList.innerHTML = '<div class="loading">Memuat events...</div>'; // Tambahkan loading state
    
        fetch('server/get_events.php') // Memanggil endpoint PHP
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok: ' + response.statusText);
                }   
                return response.text(); // Ambil respons sebagai teks
            })
            .then(text => {
                console.log('Response text:', text); // Log respons
                return JSON.parse(text); // Coba untuk mengurai sebagai JSON
            })
            .then(events => {
                displayEvents(events); // Tampilkan event
            })
            .catch(error => {
                console.error('Error mengambil event:', error);
                eventList.innerHTML = '<p>Gagal memuat event. Silakan coba lagi.</p>';
            });
    }

    // Pencarian event
    function searchEvents() {
        const searchTerm = searchInput.value.toLowerCase().trim();
    
        // Tambahkan loading state
        eventList.innerHTML = '<div class="loading">Mencari events...</div>';
    
        fetch(`server/search_events.php?term=${encodeURIComponent(searchTerm)}`)
            .then(response => response.json())
            .then(events => {
                displayEvents(events);
            })
            .catch(error => {
                console.error('Error pencarian event:', error);
                eventList.innerHTML = '<p>Gagal mencari event. Silakan coba lagi.</p>';
            });
    }

    // Event listener untuk tombol pencarian
    if (searchBtn) {
        searchBtn.addEventListener('click', searchEvents);
    }

    // Event listener untuk input pencarian (tekan enter)
    if (searchInput) {
        searchInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                searchEvents();
            }
        });
    }

    // Panggil fungsi untuk mengambil event saat halaman dimuat
    fetchEvents();
});