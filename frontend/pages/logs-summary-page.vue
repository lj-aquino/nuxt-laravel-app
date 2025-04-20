<template>
  <div class="logs-summary-page">
    <!-- Sidebar -->
    <Sidebar :activeMenu="'Logs Summary'" />
    <TopBar />

    <!-- Top Four Details -->
    <div class="top-details">
      <div class="total-entries-today" style="background-color: #e2ffed;">Total Entries Today</div>
      <div class="verified-entries" style="background-color: #fbffe0;">Verified Entries</div>
      <div class="presented-id" style="background-color: #eef6ff;">Presented ID</div>
      <div class="repeat-offenders" style="background-color: #ffefef;">Repeat Offenders</div>
    </div>

    <div class="summary-plus-pie-chart">
      <div class="summary-box">
        <table class="logs-table-summary-page">
          <thead>
            <tr>
              <th>Student No.</th>
              <th>Entry Time</th>
              <th>Status</th>
              <th>Remarks</th>
            </tr>
          </thead>
        </table>
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

export default {
  data() {
    return {
      logs: [] // Replace with actual data source
    };
  },
  methods: {
    formatStudentName(name) {
      // Add logic to format student name if needed
      return name;
    }
  }
};
</script>