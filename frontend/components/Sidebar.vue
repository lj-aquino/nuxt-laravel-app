<template>
  <div>
    <!-- Sidebar -->
    <div
      class="sidebar"
      :class="{ collapsed: isCollapsed }"
    >
      <!-- UPLB Logo -->
      <div style="position: absolute; left: 25.4px; top: 43.8px;">
        <img
          src="@/assets/images/uplb_logo.png"
          alt="UPLB Logo"
          style="width: 53.5px; height: 47.5px;"
        />
      </div>

      <!-- Face Recognition Title -->
      <div style="position: absolute; left: 90px; top: 45.4px;">
        <div
          class="face-title"
          style="font-family: 'Bricolage Grotesque', sans-serif; font-size: 25px; color: #eead2b; font-weight: bold;"
        >
          Face
        </div>
        <div
          class="recognition-title"
          style="font-family: 'Bricolage Grotesque', sans-serif; font-size: 15px; color: white; margin-top: 0.8px; font-weight: bold;"
        >
          Recognition
        </div>
      </div>

      <!-- Navigation Menu -->
      <div class="menu" style="margin-top: 120px; padding: 0;">
        <ul>
          <li>
            <a
              href="#"
              class="menu-item"
              :class="{ active: activeMenu === 'Dashboard' }"
              @click.prevent="$router.push('/face-scanning')"
            >
              <i class="fas fa-tachometer-alt"></i>
              Dashboard
            </a>
          </li>
          <li>
            <a
              href="#"
              class="menu-item"
              :class="{ active: activeMenu === 'Logs Summary' }"
              @click.prevent="$router.push('/logs-summary-page')"
            >
              <i class="fas fa-file-alt"></i>
              Logs Summary
            </a>
          </li>
          <li>
            <a
              href="#"
              class="menu-item"
              :class="{ active: activeMenu === 'Register Student' }"
              @click.prevent="$router.push('/register-student')"
            >
              <i class="fas fa-user-plus"></i>
              Register Student
            </a>
          </li>
          <li>
            <a
              href="#"
              class="menu-item"
              :class="{ active: activeMenu === 'Update Info' }"
            >
              <i class="fas fa-edit"></i>
              Update Info
            </a>
          </li>
        </ul>
      </div>

      <!-- Horizontal Line -->
      <hr style="border: 1px solid #444; margin: 16px 0; width: 90%; margin-left: auto; margin-right: auto;" />

      <!-- Account Section -->
      <div class="menu" style="margin-top: 0; padding: 0;">
        <ul>
          <li>
            <a
              href="#"
              class="menu-item"
              :class="{ active: activeMenu === 'Account' }"
            >
              <i class="fas fa-user"></i>
              Account
            </a>
          </li>
          <li>
            <a
              href="#"
              class="menu-item"
              :class="{ active: activeMenu === 'Saved Logs' }"
            >
              <i class="fas fa-save"></i>
              Saved Logs
            </a>
          </li>
          <li>
            <a
              href="#"
              class="menu-item"
              :class="{ active: activeMenu === 'Settings' }"
            >
              <i class="fas fa-cog"></i>
              Settings
            </a>
          </li>
        </ul>
      </div>

      <!-- Horizontal Line -->
      <hr style="border: 1px solid #444; margin: 16px 0; width: 90%; margin-left: auto; margin-right: auto;" />

      <!-- Footer Section -->
      <div class="menu" style="position: absolute; bottom: 16px; width: 100%; padding: 0;">
        <ul>
          <li>
            <a
              href="#"
              class="menu-item"
              :class="{ active: activeMenu === 'Admin' }"
            >
              <i class="fas fa-user-shield"></i>
              Admin
            </a>
          </li>
          <li>
            <a
              href="#"
              class="menu-item"
              :class="{ active: activeMenu === 'Help' }"
            >
              <i class="fas fa-question-circle"></i>
              Help
            </a>
          </li>
          <li>
            <a
              href="#"
              class="menu-item"
              :class="{ active: activeMenu === 'Log Out' }"
              @click.prevent="handleLogout"
            >
              <i class="fas fa-sign-out-alt"></i>
              Log Out
            </a>
          </li>
        </ul>
      </div>
    </div>

    <!-- Toggle Button for Small Screens -->
    <button
      class="toggle-button"
      @click="isCollapsed = !isCollapsed"
    >
      ☰
    </button>
    <div v-if="showLogoutModal" class="modal-overlay">
      <div class="modal-content">
        <h2>Confirm Logout</h2>
        <p>Are you sure you want to log out?</p>
        <div class="modal-buttons">
          <button class="modal-button confirm" @click="confirmLogout">
            Yes, Logout
          </button>
          <button class="modal-button cancel" @click="showLogoutModal = false">
            Cancel
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { api } from '../lib/api'

export default {
  name: 'Sidebar',
  props: {
    activeMenu: {
      type: String,
      required: true,
    },
  },
  methods: {
    handleLogout() {
      // Simply show the modal instead of logging out directly
      this.showLogoutModal = true;
    },
    async confirmLogout() {
      try {
        const { error } = await api.auth.signOut()
        if (error) throw error
        this.showLogoutModal = false;
        this.$router.push('/login')
      } catch (error) {
        console.error('Error logging out:', error.message)
      }
    }
  },
  data() {
    return {
      isCollapsed: false, // State to toggle sidebar
      showLogoutModal: false, // State to show logout confirmation modal
    };
  },
};
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Zoika:wght@400;700&display=swap');
@import url('https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:wght@400;700&display=swap');

/* Sidebar styles */
.sidebar {
  font-family: 'Bricolage Grotesque', sans-serif;
  background-color: #252525;
  width: 280px;
  height: 100vh;
  position: absolute;
  left: 0;
  top: 0;
  overflow: hidden;
  transition: transform 0.3s ease;
}

.sidebar.collapsed {
  transform: translateX(-100%); /* Hide sidebar when collapsed */
}

.menu ul {
  list-style: none;
  padding: 0;
  margin: 0;
}

.menu-item {
  text-decoration: none;
  color: white;
  display: flex;
  align-items: center;
  padding: 8px 16px;
  border-radius: 4px;
  height: 45.5px;
  width: 100%;
  transition: background-color 0.3s ease;
}

.menu-item i {
  margin-right: 12px;
  font-size: 18px;
}

.menu-item:hover,
.menu-item.active {
  font-weight: bold;
  color: white;
  background-color: #eead2b;
}

.toggle-button {
  position: absolute;
  top: 20px;
  left: 300px;
  background-color: #252525;
  color: white;
  border: none;
  border-radius: 4px;
  padding: 8px 12px;
  cursor: pointer;
  z-index: 1000;
}

.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
}

.modal-content {
  background-color: #252525;
  padding: 24px;
  border-radius: 8px;
  width: 90%;
  max-width: 400px;
  text-align: center;
}

.modal-content h2 {
  color: white;
  font-family: 'Bricolage Grotesque', sans-serif;
  margin-bottom: 16px;
  font-size: 24px;
}

.modal-content p {
  color: white;
  margin-bottom: 24px;
  font-family: 'Bricolage Grotesque', sans-serif;
}

.modal-buttons {
  display: flex;
  justify-content: center;
  gap: 16px;
}

.modal-button {
  padding: 12px 24px;
  border: none;
  border-radius: 4px;
  font-family: 'Bricolage Grotesque', sans-serif;
  font-weight: bold;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.modal-button.confirm {
  background-color: #eead2b;
  color: white;
}

.modal-button.confirm:hover {
  background-color: #d69b24;
}

.modal-button.cancel {
  background-color: #444;
  color: white;
}

.modal-button.cancel:hover {
  background-color: #555;
}

/* Responsive Styles */
@media (max-width: 768px) {
  .sidebar {
    transform: translateX(-100%); /* Hide sidebar by default on small screens */
  }

  .sidebar.collapsed {
    transform: translateX(0); /* Show sidebar when toggled */
  }

  .toggle-button {
    left: 20px; /* Adjust position of toggle button on small screens */
  }
}

/* Hide toggle button on larger screens */
@media (min-width: 769px) {
  .toggle-button {
    display: none; /* Hide the toggle button */
  }
}
</style>