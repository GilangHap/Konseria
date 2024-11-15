// Variabel Global
let totalPrice = 0;
const ADMIN_FEE = 5000;

// Fungsi untuk menambah item ke keranjang
function addToCart(event) {
  let cartItems = JSON.parse(localStorage.getItem('cartItems')) || [];

  const price = typeof event.ticketPrice === 'string' 
      ? parseFloat(event.ticketPrice.replace(/[^0-9.-]+/g, "")) 
      : event.ticketPrice;

  const existingItemIndex = cartItems.findIndex(item => item.id === event.id);

  if (existingItemIndex > -1) {
      cartItems[existingItemIndex].quantity += 1;
  } else {
      cartItems.push({
          ...event,
          ticketPrice: price,
          quantity: 1
      });
  }

  localStorage.setItem('cartItems', JSON.stringify(cartItems));
}

// Render Item Keranjang
function renderCartItems() {
  const cartItemsList = document.getElementById('cartItemsList');
  cartItemsList.innerHTML = '';

  const cartItems = JSON.parse(localStorage.getItem('cartItems')) || [];

  if (cartItems.length === 0) {
      cartItemsList.innerHTML = '<p>Keranjang Anda kosong</p>';
      document.getElementById('subtotalPrice').textContent = 'Rp 0';
      document.getElementById('totalPrice').textContent = 'Rp 0';
      return;
  }

  cartItems.forEach(item => {
      const cartItemElement = document.createElement('div');
      cartItemElement.classList.add('cart-item');
      cartItemElement.innerHTML = `
          <div class="item-details">
              <div>
                  <h3>${item.title}</h3>
                  <p>Rp ${item.ticketPrice.toLocaleString()} x ${item.quantity}</p>
              </div>
          </div>
          <div class="item-actions">
              <button onclick="updateQuantity('${item.id}', -1)">-</button>
              <span>${item.quantity}</span>
              <button onclick="updateQuantity('${item.id}', 1)">+</button>
              <button onclick="removeFromCart('${item.id}')">Hapus</button>
          </div>
      `;
      cartItemsList.appendChild(cartItemElement);
  });
  updateOrderSummary();
}

// Update Kuantitas
function updateQuantity(eventId, change) {
  let cartItems = JSON.parse(localStorage.getItem('cartItems')) || [];

  const itemIndex = cartItems.findIndex(item => item.id === eventId);

  if (itemIndex > -1) {
      cartItems[itemIndex].quantity = Math.max(1, cartItems[itemIndex].quantity + change);

      if (cartItems[itemIndex].quantity === 0) {
          cartItems.splice(itemIndex, 1);
      }

      localStorage.setItem('cartItems', JSON.stringify(cartItems));
      renderCartItems();
  }
}

// Hapus Item dari Keranjang
function removeFromCart(eventId) {
  let cartItems = JSON.parse(localStorage.getItem('cartItems')) || [];
  cartItems = cartItems.filter(item => item.id !== eventId);

  localStorage.setItem('cartItems', JSON.stringify(cartItems));
  renderCartItems();
}

// Update Ringkasan Pesanan
function updateOrderSummary() {
  const cartItems = JSON.parse(localStorage.getItem('cartItems')) || [];

  const subtotal = cartItems.reduce((total, item) => {
      return total + (item.ticketPrice * item.quantity);
  }, 0);

  totalPrice = subtotal + ADMIN_FEE;

  document.getElementById('subtotalPrice').textContent = `Rp ${subtotal.toLocaleString()}`;
  document.getElementById('totalPrice').textContent = `Rp ${totalPrice.toLocaleString()}`;
}

// Proses Pembayaran
// Proses Pembayaran
async function processCheckout() {
  const fullName = document.getElementById('fullName').value;
  const email = document.getElementById('email').value;
  const whatsapp = document.getElementById('whatsapp').value;

  if (!fullName || !email || !whatsapp) {
      alert('Harap lengkapi semua data');
      return;
  }

  const cartItems = JSON.parse(localStorage.getItem('cartItems')) || [];

  if (cartItems.length === 0) {
      alert('Keranjang Anda kosong!');
      return;
  }
  const subtotal = cartItems.reduce((total, item) => total + (item.ticketPrice * item.quantity), 0);
  const totalWithAdminFee = subtotal + ADMIN_FEE; // Menambahkan biaya administrasi
  // Tambahkan bagian ini untuk membuat orderId
  const orderId = 'order_' + new Date().getTime();  // Anda bisa membuat orderId sesuai kebutuhan
  
  // Mempersiapkan data yang akan dikirim ke backend
  const orderData = {
      orderId: orderId,  // Kirimkan orderId ke backend
      customer: { fullName, email, whatsapp },
      items: cartItems,
      total: totalWithAdminFee  
  };

  try {
      const response = await fetch('http://localhost/konseria/server/get_snap_token.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(orderData)  // Mengirimkan orderData
      });

      const result = await response.json();
      console.log('Server Response:', result);

      if (result.token) {
          // Panggil Snap Dialog Midtrans
          window.snap.pay(result.token, {
              onSuccess: function (res) {
                  alert('Pembayaran berhasil!');
                  localStorage.removeItem('cartItems');
                  window.location.href = '/thank-you.html';
              },
              onPending: function (res) {
                  alert('Pembayaran menunggu konfirmasi.');
              },
              onError: function (res) {
                  alert('Pembayaran gagal.');
              },
              onClose: function () {
                  alert('Anda menutup dialog pembayaran.');
              },
          });
      } else {
          alert('Gagal mendapatkan token pembayaran.');
      }
  } catch (error) {
      console.error('Error:', error);
      alert('Terjadi kesalahan saat memproses pembayaran.');
  }
}


// Inisialisasi
document.addEventListener('DOMContentLoaded', () => {
  renderCartItems();
  const checkoutButton = document.getElementById('checkoutButton');
  if (checkoutButton) {
      checkoutButton.addEventListener('click', processCheckout);
  }
});
