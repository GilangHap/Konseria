// Variabel Global
let totalPrice = 0;
const ADMIN_FEE_PER_ITEM = 5000;

// Fungsi untuk menambah item ke keranjang
function addToCart(event) {
  let cartItems = JSON.parse(localStorage.getItem("cartItems")) || [];

  const price =
    typeof event.ticketPrice === "string"
      ? parseFloat(event.ticketPrice.replace(/[^0-9.-]+/g, ""))
      : event.ticketPrice;

  const existingItemIndex = cartItems.findIndex((item) => item.id === event.id);

  if (existingItemIndex > -1) {
    cartItems[existingItemIndex].quantity += 1;
  } else {
    cartItems.push({
      ...event,
      ticketPrice: price,
      quantity: 1,
    });
  }

  // Menyimpan kembali data ke localStorage dan memanggil render ulang
  localStorage.setItem("cartItems", JSON.stringify(cartItems));
  renderCartItems();
}

// Render Item Keranjang
function renderCartItems() {
  const cartItemsList = document.getElementById("cartItemsList");
  cartItemsList.innerHTML = "";

  // Gunakan cartItems dari localStorage dan set global cartItems
  const cartItems = JSON.parse(localStorage.getItem("cartItems")) || [];

  if (cartItems.length === 0) {
    cartItemsList.innerHTML = "<p>Keranjang Anda kosong</p>";
    document.getElementById("subtotalPrice").textContent = "Rp 0";
    document.getElementById("adminFee").textContent = "Rp 0";
    document.getElementById("totalPrice").textContent = "Rp 0";
    return;
  }

  cartItems.forEach((item) => {
    const cartItemElement = document.createElement("div");
    cartItemElement.classList.add("cart-item");
    cartItemElement.innerHTML = `
      <div class="item-details">
        <div>
          <h3>${item.title}</h3>
          <p>Rp ${item.ticketPrice.toLocaleString()} x ${item.quantity}</p>
        </div>
      </div>
      <div class="item-actions">
        <button class="btn-decrease">-</button>
        <span>${item.quantity}</span>
        <button class="btn-increase">+</button>
        <button class="btn-delete">Hapus</button>
      </div>
    `;
  
    // Tambahkan event listener
    cartItemElement.querySelector(".btn-decrease").addEventListener("click", () => {
      updateQuantity(item.id, -1);
    });
  
    cartItemElement.querySelector(".btn-increase").addEventListener("click", () => {
      updateQuantity(item.id, 1);
    });
  
    cartItemElement.querySelector(".btn-delete").addEventListener("click", () => {
      removeFromCart(item.id);
    });
  
    cartItemsList.appendChild(cartItemElement);
  });
  
  updateOrderSummary();
}

// Update Kuantitas
function updateQuantity(eventId, change) {
  let cartItems = JSON.parse(localStorage.getItem("cartItems")) || [];
  const itemIndex = cartItems.findIndex((item) => item.id === eventId);

  if (itemIndex > -1) {
    // Perbarui jumlah, tetapi pastikan jumlahnya tidak kurang dari 1
    cartItems[itemIndex].quantity = Math.max(
      1,
      cartItems[itemIndex].quantity + change
    );

    if (cartItems[itemIndex].quantity === 0) {
      cartItems.splice(itemIndex, 1); // Hapus item jika quantity menjadi 0
    }

    localStorage.setItem("cartItems", JSON.stringify(cartItems));
    renderCartItems(); // Re-render untuk menampilkan jumlah yang diperbarui
  }
}

// Hapus Item dari Keranjang
function removeFromCart(eventId) {
  let cartItems = JSON.parse(localStorage.getItem("cartItems")) || [];
  cartItems = cartItems.filter((item) => item.id !== eventId);

  localStorage.setItem("cartItems", JSON.stringify(cartItems));
  renderCartItems(); // Re-render untuk memperbarui tampilan setelah item dihapus
}

// Update Ringkasan Pesanan
function updateOrderSummary() {
  const cartItems = JSON.parse(localStorage.getItem("cartItems")) || [];

  // Hitung subtotal dan total quantity
  const subtotal = cartItems.reduce((total, item) => {
    return total + item.ticketPrice * item.quantity;
  }, 0);

  const totalQuantity = cartItems.reduce((total, item) => {
    return total + item.quantity;
  }, 0);

  // Hitung admin fee berdasarkan total quantity
  const adminFee = totalQuantity * ADMIN_FEE_PER_ITEM;
  totalPrice = subtotal + adminFee;

  // Update tampilan subtotal, admin fee, dan total harga
  document.getElementById("subtotalPrice").textContent = `Rp ${subtotal.toLocaleString()}`;
  document.getElementById("adminFee").textContent = `Rp ${adminFee.toLocaleString()}`;
  document.getElementById("totalPrice").textContent = `Rp ${totalPrice.toLocaleString()}`;
}


// Fungsi untuk memproses checkout
async function processCheckout() {
  const fullName = document.getElementById("fullName").value;
  const email = document.getElementById("email").value;
  const whatsapp = document.getElementById("whatsapp").value;

  if (!fullName || !email || !whatsapp) {
    alert("Harap lengkapi semua data");
    return;
  }

  const cartItems = JSON.parse(localStorage.getItem("cartItems")) || [];

  if (cartItems.length === 0) {
    alert("Keranjang Anda kosong!");
    return;
  }

  const subtotal = cartItems.reduce(
    (total, item) => total + item.ticketPrice * item.quantity,
    0
  );
  const totalQuantity = cartItems.reduce((total, item) => total + item.quantity, 0);
  const totalWithAdminFee = subtotal + totalQuantity * ADMIN_FEE_PER_ITEM;
  const orderId = "order_" + new Date().getTime();

  const orderData = {
    orderId: orderId,
    customer: { fullName, email, whatsapp },
    items: cartItems,
    total: totalWithAdminFee,
  };

  try {
    const response = await fetch(
      "http://localhost/konseria/server/get_snap_token.php",
      {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(orderData),
      }
    );

    const result = await response.json();

    if (result.token) {
      window.snap.pay(result.token, {
        onSuccess: async function () {
          // Update stok tiket di server
          const stockUpdateResponse = await fetch(
            "http://localhost/konseria/server/update_ticket_stock.php",
            {
              method: "POST",
              headers: { "Content-Type": "application/json" },
              body: JSON.stringify({ items: cartItems }),
            }
          );

          const stockResult = await stockUpdateResponse.json();
          if (stockResult.success) {
            // Simpan struk dan alihkan ke halaman Thank You
            localStorage.setItem("orderReceipt", JSON.stringify(orderData));
            localStorage.removeItem("cartItems");
            window.location.href = "/konseria/thank_you.html";
          } else {
            alert("Gagal memperbarui stok tiket. Hubungi admin.");
          }
        },
        onPending: function () {
          alert("Pembayaran menunggu konfirmasi.");
        },
        onError: function () {
          alert("Pembayaran gagal.");
        },
        onClose: function () {
          alert("Anda menutup dialog pembayaran.");
        },
      });
    } else {
      alert("Gagal mendapatkan token pembayaran.");
    }
  } catch (error) {
    console.error("Error:", error);
    alert("Terjadi kesalahan saat memproses pembayaran.");
  }
}



// Inisialisasi
document.addEventListener("DOMContentLoaded", () => {
  renderCartItems(); // Render item keranjang saat halaman dimuat
  const checkoutButton = document.getElementById("checkoutButton");
  if (checkoutButton) {
    checkoutButton.addEventListener("click", processCheckout);
  }
});

document.addEventListener("DOMContentLoaded", () => {
  // Ambil URL halaman sebelumnya dari localStorage
  const backButton = document.getElementById("backButton");
  const previousEventUrl = localStorage.getItem("previousEventUrl");

  // Tetapkan href pada tombol kembali, jika tersedia
  if (previousEventUrl) {
    backButton.href = previousEventUrl;
  } else {
    backButton.href = "index.html"; // Default kembali ke halaman utama
  }
});
