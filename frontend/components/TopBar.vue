<template>
  <div class="topbar">
    <!-- Welcome and Logs Section -->
    <div class="welcome-section">
      <div class="welcome-message">Welcome back, Security</div>
      <div class="detailed-logs-message">Let’s take a detailed look at verification logs.</div>
    </div>

    <!-- Search Bar -->
    <div class="search-section">
      <input
        type="text"
        placeholder="Search here"
        class="search-bar"
      />
      <div class="search-icon">
        <i class="fas fa-search"></i>
      </div>
    </div>

    <!-- Profile Section -->
    <div class="profile-section">
      <div class="profile-text">
        <span class="profile-name">Mang Sekyu</span>
        <span class="profile-role">Security Guard</span>
      </div>
      <div class="profile-pic">
        <img
          src="@/assets/images/lj-2.png"
          alt="User Profile"
        />
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'TopBar',
};
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:wght@400;700&display=swap');
@import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css'); /* Import Font Awesome for icons */

html,
body {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

/* Top Bar Container */
.topbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background-color: #71231c;
  color: white;
  padding: 16px 24px;
  width: 110%;
  height: 22%;
  box-sizing: border-box;
  font-family: 'Bricolage Grotesque', sans-serif;
}

/* Welcome Section */
.welcome-section {
  flex: 1;
}

.welcome-message {
  font-size: 30px;
  font-weight: bold;
  margin-left: 300px;
}

.detailed-logs-message {
  font-size: 20px;
  margin-top: 4px;
  margin-left: 300px;
}

/* Search Section */
.search-section {
  display: flex;
  align-items: center;
  gap: 5px;
  margin-right: 80px;
}

.search-bar {
  background-color: white;
  color: black;
  border-radius: 100px;
  padding: 8px 16px;
  border: none;
  width: 200px;
}

.search-icon {
  background-color: #eead2b;
  border: 1px solid white;
  border-radius: 50%;
  width: 29px;
  height: 29px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.search-icon i {
  color: white;
  font-size: 16px;
}

/* Profile Section */
.profile-section {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-right: 50px;
}

.profile-text {
  text-align: right;
}

.profile-name {
  font-size: 16px;
  font-weight: bold;
  display: block;
}

.profile-role {
  font-size: 12px;
  color: #d1d5db;
}

.profile-pic img {
  width: 40px;
  height: 40px;
  border: 2px solid white;
  border-radius: 50%;
}

/* Responsive Styles */
@media (max-width: 768px) {
  .topbar {
    flex-direction: column;
    align-items: flex-start;
    padding: 16px;
    height: auto;
  }

  .welcome-section {
    margin-bottom: 16px;
  }

  .search-section {
    width: 100%;
    justify-content: space-between;
    margin-bottom: 16px;
  }

  .search-bar {
    width: 80%;
  }

  .profile-section {
    width: 100%;
    justify-content: space-between;
  }

  .profile-text {
    text-align: left;
  }

  .welcome-message {
    margin-left: 40px; /* Remove left margin for mobile screens */
    font-size: 20px; /* Adjust font size for mobile screens */
  }

  .detailed-logs-message {
    margin-left: 0;
    font-size: 16px; /* Adjust font size for mobile screens */
  }
}

/* iPad Air Styles (820px x 1180px) */
@media (min-width: 820px) and (max-width: 1180px) {
  .topbar {
    align-items: center;
    padding: 20px;
    height: auto;
    display: block;
  }

  .welcome-section {
    margin-left: 260px;
  }

  .search-section {
    margin-top: 20px;
    margin-left: 260px;
  }

  .search-bar {
    width: 250px; /* Slightly larger search bar for iPad Air */
  }

  .profile-section {
    margin-right: 30px;
  }

  .welcome-message {
    font-size: 28px; /* Larger font size for iPad Air */
  }

  .detailed-logs-message {
    font-size: 18px; /* Larger font size for iPad Air */
  }
}
</style>