document.addEventListener("DOMContentLoaded", () => {
    const eventList = document.getElementById("eventList");
    const searchInput = document.getElementById("eventSearch");
    const searchBtn = document.getElementById("searchBtn");
  
    // Fungsi untuk menampilkan event
    function displayEvents(events) {
      eventList.innerHTML = ""; // Bersihkan daftar event sebelumnya
  
      if (events.length === 0) {
        eventList.innerHTML = "<p>Tidak ada event ditemukan.</p>";
        return;
      }
  
      events.forEach((event) => {
        const eventLink = document.createElement("a");
        eventLink.href = `detail.html?id=${event.id}`;
        eventLink.classList.add("event-card-link");
        eventLink.style.textDecoration = "none";
  
        const eventCard = document.createElement("div");
        eventCard.classList.add("event-card");
  
        eventCard.innerHTML = `
          <img src="${event.imageURL || "/images/default-image.jpg"}" alt="${event.title || "Event"}">
          <h3>${event.title || "Judul Tidak Tersedia"}</h3>
          <p>📅 ${formatDate(event.date) || "Tanggal Tidak Tersedia"}</p>
          <p>📍 ${event.location || "Lokasi Tidak Tersedia"}</p>
          <p>💰 ${
            event.ticketPrice
              ? formatRupiah(event.ticketPrice)
              : "Harga Tidak Tersedia"
          }</p>
          <button>Beli Tiket</button>
        `;
  
        eventLink.appendChild(eventCard);
        eventList.appendChild(eventLink);
      });
    }
  
    // Fungsi untuk memformat tanggal
    function formatDate(dateString) {
      const months = [
        "Januari",
        "Februari",
        "Maret",
        "April",
        "Mei",
        "Juni",
        "Juli",
        "Agustus",
        "September",
        "Oktober",
        "November",
        "Desember",
      ];
      const date = new Date(dateString);
      const day = date.getDate();
      const month = months[date.getMonth()];
      const year = date.getFullYear();
      return `${day} ${month} ${year}`;
    }
  
    // Fungsi untuk memformat rupiah
    function formatRupiah(value) {
      return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
      }).format(value);
    }
  
    // Ambil event dari server
    function fetchEvents() {
      eventList.innerHTML = '<div class="loading">Memuat events...</div>'; // Tambahkan loading state
  
       fetch("server/get_all_events.php")
        .then((response) => {
          if (!response.ok) {
            throw new Error("Network response was not ok");
          }
          return response.json(); // Parsing response ke JSON
        })
        .then((events) => {
          displayEvents(events); // Menampilkan event
        })
        .catch((error) => {
          console.error("Error mengambil event:", error);
          eventList.innerHTML = "<p>Gagal memuat event. Silakan coba lagi.</p>";
        });
    }
  
    // Pencarian event
    function searchEvents() {
      const searchTerm = searchInput.value.toLowerCase().trim();
      eventList.innerHTML = '<div class="loading">Mencari events...</div>';
  
      fetch(`server/search_events.php?term=${encodeURIComponent(searchTerm)}`)
        .then((response) => response.json())
        .then((events) => {
          displayEvents(events);
        })
        .catch((error) => {
          console.error("Error pencarian event:", error);
          eventList.innerHTML = "<p>Gagal mencari event. Silakan coba lagi.</p>";
        });
    }
  
    // Event listener untuk tombol pencarian
    if (searchBtn) {
      searchBtn.addEventListener("click", searchEvents);
    }
  
    // Event listener untuk input pencarian (tekan enter)
    if (searchInput) {
      searchInput.addEventListener("keypress", (e) => {
        if (e.key === "Enter") {
          searchEvents();
        }
      });
    }
  
    // Panggil fungsi untuk mengambil event saat halaman dimuat
    fetchEvents();
  });