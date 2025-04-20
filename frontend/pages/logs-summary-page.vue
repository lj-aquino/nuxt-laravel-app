<template>
  <div class="logs-summary-page">
    <!-- Sidebar -->
    <Sidebar :activeMenu="'Logs Summary'" />
    <TopBar />

    <!-- Top Four Details -->
    <div class="top-details">
      <div class="detail-box" style="background-color: #e2ffed;">First Square</div>
      <div class="detail-box" style="background-color: #fbffe0;">Second Square</div>
      <div class="detail-box" style="background-color: #eef6ff;">Third Square</div>
      <div class="detail-box" style="background-color: #ffefef;">Fourth Square</div>
    </div>

    <div class="summary-plus-pie-chart">
      <div class="summary-box">
        <!-- Pass minimalView as true -->
        <LogsSummary :logs="logs" :minimalView="true" />
      </div>

      <div class="pie-chart">
        Pie Chart
      </div>

    </div>

  </div>
</template>

<script>
import Sidebar from '~/components/Sidebar.vue';
import TopBar from '~/components/TopBar.vue';
import '~/assets/css/logs-summary-page.css';
</script>
