<template>
  <div
    class="topbar text-white flex justify-between items-center"
    style="background-color: #71231c !important; width: 1380.4px; height: 134.9px; position: absolute; left: 162.6px; top: 0px;"
  >
    <!-- Welcome Message -->
    <div class="welcome-message" style="position: absolute; left: 285.4px; top: 50px; font-size: 22px;">
      Welcome back, Security
    </div>

    <!-- Detailed Logs Message -->
    <div
      class="detailed-logs-message"
      style="position: absolute; left: 285.4px; top: 87.1px; font-size: 10px;"
    >
      Let’s take a detailed look at verification logs.
    </div>

    <!-- Search Bar -->
    <div class="flex items-center" style="position: absolute; right: 380px; top: 50px;">
      <input
        type="text"
        placeholder="Search here"
        class="search-bar"
        style="background-color: white; color: black; border-radius: 100px; padding: 8px 16px; width: 256px;"
      />
      <button
        class="search-button"
        style="background-color: #facc15; color: black; padding: 8px 16px; border-radius: 0 100px 100px 0;"
      >
        Search
      </button>
    </div>

    <!-- Profile Section -->
    <div class="flex items-center" style="position: absolute; left: 1177.5px; top: 50px;">
      <img
        src="@/assets/images/profile-image.jpg"
        alt="User Profile"
        style="width: 52.5px; height: 52.5px; border: 2px solid white; border-radius: 50%; margin-right: 8px;"
      />
      <div>
        <span class="profile-name" style="font-size: 14px; display: block; position: absolute; right: 80px; top: 10px;">
          Mang Celvin
        </span>
        <span class="profile-role" style="font-size: 10px; color: #d1d5db; position: absolute; right: 80px; top: 45px;">
          Security Guard
        </span>
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