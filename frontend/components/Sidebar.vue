<!-- filepath: c:\Users\LJ\Desktop\Academics\2nd Sem 2024-2025\nuxt-laravel-app\frontend\components\Sidebar.vue -->
<template>
  <div
    class="sidebar"
    style="background-color: #252525; width: 245.5px; height: 800px; position: absolute; left: 0px; top: 0px;"
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
          >
            <i class="fas fa-tachometer-alt"></i>
            Dashboard
          </a>
        </li>
        <li>
          <a
            href="#"
            class="menu-item"
          >
            <i class="fas fa-file-alt"></i>
            Detailed Logs
          </a>
        </li>
        <li>
          <a
            href="#"
            class="menu-item"
          >
            <i class="fas fa-user-plus"></i>
            Register Student
          </a>
        </li>
        <li>
          <a
            href="#"
            class="menu-item"
          >
            <i class="fas fa-edit"></i>
            Update Info
          </a>
        </li>
        <li>
          <a
            href="#"
            class="menu-item"
          >
            <i class="fas fa-bell"></i>
            Notifications
          </a>
        </li>
      </ul>
    </div>

    <!-- Account Section -->
    <div class="menu" style="margin-top: 20px; padding: 0;">
      <ul>
        <li>
          <a
            href="#"
            class="menu-item"
          >
            <i class="fas fa-user"></i>
            Account
          </a>
        </li>
        <li>
          <a
            href="#"
            class="menu-item"
          >
            <i class="fas fa-save"></i>
            Saved Logs
          </a>
        </li>
        <li>
          <a
            href="#"
            class="menu-item"
          >
            <i class="fas fa-cog"></i>
            Settings
          </a>
        </li>
      </ul>
    </div>

    <!-- Footer Section -->
    <div class="menu" style="position: absolute; bottom: 16px; width: 100%; padding: 0;">
      <ul>
        <li>
          <a
            href="#"
            class="menu-item"
          >
            <i class="fas fa-user-shield"></i>
            Admin
          </a>
        </li>
        <li>
          <a
            href="#"
            class="menu-item"
          >
            <i class="fas fa-question-circle"></i>
            Help
          </a>
        </li>
        <li>
          <a
            href="#"
            class="menu-item"
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
};
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Zoika:wght@400;700&display=swap');
@import url('https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:wght@400;700&display=swap');

/* Sidebar styles */
.sidebar {
  font-family: 'Bricolage Grotesque', sans-serif;
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
  width: 245.5px;
  transition: background-color 0.3s ease;
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
}
</style>