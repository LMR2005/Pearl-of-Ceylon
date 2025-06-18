<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    .popup-overlay {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.5);
      z-index: 1000;
      justify-content: center;
      align-items: center;
    }
    .popup-content {
      background: white;
      padding: 2rem;
      border-radius: 0.5rem;
      width: 100%;
      max-width: 400px;
      position: relative;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    .popup-content .close-btn {
      position: absolute;
      top: 0.5rem;
      right: 0.5rem;
      font-size: 1.5rem;
      cursor: pointer;
    }
    .popup-overlay.active {
      display: flex;
    }
  </style>
</head>
<body>
  <div id="popupOverlay" class="popup-overlay">
    <div class="popup-content">
      <span class="close-btn" onclick="closePopup()">×</span>
      <h2 id="popupTitle" class="text-2xl font-bold mb-6 text-center">Login</h2>
      <div class="mb-4">
        <button id="toggleForm" class="text-blue-500 hover:underline" onclick="toggleForm()">
          Need an account? Register
        </button>
      </div>
      <div>
        <div class="mb-4">
          <label class="block text-gray-700 mb-2" for="username">Username</label>
          <input
            type="text"
            id="username"
            class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
            required
          />
        </div>
        <div class="mb-6">
          <label class="block text-gray-700 mb-2" for="password">Password</label>
          <input
            type="password"
            id="password"
            class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
            required
          />
        </div>
        <button
          onclick="handleSubmit()"
          class="w-full bg-blue-500 text-white py-2 rounded-lg hover:bg-blue-600 transition"
        >
          Login
        </button>
        <p id="message" class="mt-4 text-center text-red-500 hidden"></p>
      </div>
    </div>
  </div>

  <script>
    let isLogin = true;

    function openPopup() {
      document.getElementById('popupOverlay').classList.add('active');
    }

    function closePopup() {
      document.getElementById('popupOverlay').classList.remove('active');
      resetForm();
    }

    function toggleForm() {
      isLogin = !isLogin;
      document.getElementById('popupTitle').textContent = isLogin ? 'Login' : 'Register';
      document.getElementById('toggleForm').textContent = isLogin ? 'Need an account? Register' : 'Already have an account? Login';
      document.querySelector('button[onclick="handleSubmit()"]').textContent = isLogin ? 'Login' : 'Register';
      resetForm();
    }

    function resetForm() {
      document.getElementById('username').value = '';
      document.getElementById('password').value = '';
      document.getElementById('message').classList.add('hidden');
    }

    async function handleSubmit() {
      const username = document.getElementById('username').value;
      const password = document.getElementById('password').value;
      const messageEl = document.getElementById('message');
      if (!username || !password) {
        messageEl.textContent = 'Username and password are required';
        messageEl.classList.remove('hidden');
        return;
      }

      const endpoint = isLogin ? '/api/login' : '/api/register';
      try {
        const response = await fetch(endpoint, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ username, password }),
        });
        const data = await response.json();
        messageEl.textContent = data.message;
        messageEl.classList.remove('hidden');
        if (response.ok && isLogin) {
          setTimeout(closePopup, 1000);
        }
      } catch (error) {
        messageEl.textContent = 'Error: Could not connect to server';
        messageEl.classList.remove('hidden');
      }
    }

    // Automatically open popup if loaded directly (optional, for testing)
    if (window.location.pathname.includes('popup.html')) {
      openPopup();
    }
  </script>
</body>
</html>