<template>
  <div
    class="topbar text-white flex justify-between items-center"
    style="background-color: #71231c !important; width: 1373px; height: 150px; position: absolute; left: 162.6px; top: 0px;"
  >
    <!-- Welcome Message -->
    <div class="welcome-message" style="position: absolute; left: 190px; top: 50px; font-size: 30px;">
      Welcome back, Security
    </div>

    <!-- Detailed Logs Message -->
    <div
      class="detailed-logs-message"
      style="position: absolute; left: 190px; top: 87.1px; font-size: 20px;"
    >
      Let’s take a detailed look at verification logs.
    </div>

    <!-- Search Bar -->
    <div class="flex items-center" style="position: absolute; right: 380px; top: 50px; gap: 8px;">
      <input
        type="text"
        placeholder="Search here"
        class="search-bar"
        style="background-color: white; color: black; border-radius: 100px; padding: 8px 16px; width: 256px; flex-shrink: 0;"
      />
      <div
        class="search-icon"
        style="background-color: #eead2b; border: 1px solid white; border-radius: 50%; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;"
      >
        <i class="fas fa-search" style="color: white; font-size: 16px;"></i>
      </div>
    </div>
    
    <!-- Profile Section -->
    <div
      class="profile-text"
      style="position: absolute; left: 1040px; top: 60px; text-align: right;"
    >
      <span class="profile-name" style="font-size: 20px; font-weight: bold; display: block;">
        Mang Sekyu
      </span>
      <span class="profile-role" style="font-size: 10px; color: #d1d5db; display: block;">
        Security Guard
      </span>
    </div>
    <div
      class="profile-pic"
      style="position: absolute; left: 1177.5px; top: 50px;"
    >
      <img
        src="@/assets/images/lj-2.png"
        alt="User Profile"
        style="width: 52.5px; height: 52.5px; border: 2px solid white; border-radius: 50%;"
      />
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

.topbar {
  font-family: 'Bricolage Grotesque', sans-serif;
}

.welcome-message,
.detailed-logs-message,
.profile-name,
.profile-role {
  color: white;
}

.welcome-message {
  font-weight: bold;
}

.profile-name {
  font-weight: bold;
}
</style>