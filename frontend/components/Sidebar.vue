<template>
  <div
    class="sidebar"
    style="background-color: #252525; width: 280px; height: 100vh; position: absolute; left: 0px; top: 0px; overflow: hidden;"
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
    <div style="position: absolute; left: 90px; top: 50.4px;">
      <div
        class="face-title"
        style="font-family: 'Zoika', sans-serif; font-size: 18px; color: #eead2b;"
      >
        Face
      </div>
      <div
        class="recognition-title"
        style="font-family: 'Bricolage Grotesque', sans-serif; font-size: 12px; color: white; margin-top: 5px;"
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
          >
            <i class="fas fa-sign-out-alt"></i>
            Log Out
          </a>
        </li>
      </ul>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Sidebar',
  props: {
    activeMenu: {
      type: String,
      required: true,
    },
  },
};
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Zoika:wght@400;700&display=swap');
@import url('https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:wght@400;700&display=swap');

/* Sidebar styles */
.sidebar {
  font-family: 'Bricolage Grotesque', sans-serif;
  overflow: hidden; /* Prevent scrollbars */
}

/* Remove bullet points and underlining */
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
  width: 280px;
  transition: background-color 0.3s ease;
  box-sizing: border-box; /* Ensure padding doesn't affect width */
}

/* Add spacing for icons */
.menu-item i {
  margin-right: 12px;
  font-size: 18px;
}

/* Highlight selected or hovered menu item */
.menu-item:hover,
.menu-item.active {
  font-weight: bold;
  color: white;
  background-color: #eead2b;
  height: 45.5px; /* Explicitly set height */
  width: 280px; /* Explicitly set width */
  box-sizing: border-box; /* Ensure padding doesn't affect dimensions */
}
</style>