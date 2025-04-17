<template>
  <div class="logs-summary-page">
    <!-- Sidebar -->
    <Sidebar :activeMenu="'Logs Summary'" />

    <!-- Main Content -->
    <div class="main-content">
      <!-- Top Bar -->
      <TopBar />

      <!-- Graphs Section -->
      <div class="graphs-section">
        <div class="graph-card">
          <h3>Total Entries Today</h3>
          <div class="graph-value">{{ totalEntriesToday }}</div>
        </div>
        <div class="graph-card">
          <h3>Verified Entries</h3>
          <div class="graph-value">{{ verifiedEntries }}</div>
        </div>
        <div class="graph-card">
          <h3>Unverified Entries</h3>
          <div class="graph-value">{{ unverifiedEntries }}</div>
        </div>
      </div>

      <!-- Table Section -->
      <div class="table-container-wrapper">
        <table class="info-table">
          <thead>
            <tr>
              <th>Student No.</th>
              <th>Entry Time</th>
              <th>Status</th>
              <th>Remarks</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="log in filteredLogs" :key="log.id">
              <td>
                <div class="student-number">{{ log.student_number }}</div>
                <div class="student-name-logs">{{ formatStudentName(log.student_name) }}</div>
              </td>
              <td>
                <div class="time">{{ log.time }}</div>
                <div class="date">{{ log.date }}</div>
              </td>
              <td>
                <div v-if="log.status === 'verified'" class="verified-status">
                  <i class="fas fa-check"></i> Verified
                </div>
                <div v-else class="unverified-status">
                  Unverified
                </div>
              </td>
              <td>
                <div class="remarks">{{ log.enrolled ? 'Enrolled' : 'Not Enrolled' }}</div>
                <div class="remarks-detail">{{ log.has_id ? 'Presented ID' : 'No ID Presented' }}</div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script>
import Sidebar from '~/components/Sidebar.vue';
import TopBar from '~/components/TopBar.vue';

export default {
  name: 'LogsSummaryPage',
  components: {
    Sidebar,
    TopBar,
  },
  data() {
    return {
      logs: [], // Store logs fetched from the backend
      filteredLogs: [],
      totalEntriesToday: 0,
      verifiedEntries: 0,
      unverifiedEntries: 0,
    };
  },
  methods: {
    async fetchLogs() {
      try {
        const result = await $fetch('https://sp-j16t.onrender.com/api/logs/recent', {
          method: 'GET',
          headers: {
            'X-API-KEY': 'yFITiurVNg9eEXIReziZQQA4iHDlCaZSDxwUCpY9SAsMO36M6OIsRl2MErKBOn9q',
          },
        });

        if (result.success) {
          this.logs = result.data.map((log) => {
            const [date, time] = log.entry_time.split(' ');
            const formattedTime = time.slice(0, 5); // Remove seconds
            return { ...log, date, time: formattedTime };
          });

          this.filteredLogs = this.logs;
          this.calculateGraphData();
        } else {
          console.error('Failed to fetch logs:', result);
        }
      } catch (error) {
        console.error('Error fetching logs:', error);
      }
    },
    calculateGraphData() {
      const today = new Date().toISOString().split('T')[0];
      const todayLogs = this.logs.filter((log) => log.date === today);

      this.totalEntriesToday = todayLogs.length;
      this.verifiedEntries = todayLogs.filter((log) => log.status === 'verified').length;
      this.unverifiedEntries = todayLogs.filter((log) => log.status === 'unverified').length;
    },
    formatStudentName(name) {
      const parts = name.split(' ');
      return parts
        .map((part, index) =>
          index === parts.length - 1
            ? part.charAt(0) + '.'
            : part.charAt(0) + '*'.repeat(part.length - 2) + part.charAt(part.length - 1)
        )
        .join(' ');
    },
  },
  mounted() {
    this.fetchLogs();
  },
};
</script>

<style scoped>
@import '~/assets/css/logs-summary-page.css';
</style>