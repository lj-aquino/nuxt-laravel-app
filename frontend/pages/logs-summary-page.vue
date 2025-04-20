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
        <table class="table-summary-page">
          <thead>
            <tr>
              <th>Student No.</th>
              <th>Entry Time</th>
              <th>Status</th>
              <th>Remarks</th>
            </tr>
          </thead>
        </table>

        <div class="table-container-wrapper-page">
          <table class="info-table-page">
            <tbody>
              <tr
                v-for="log in logs"
                :key="log.id"
                style="border-bottom: 1px solid #e8e8e8;"
              >
                <td>
                  <div class="student-number-page">{{ log.student_number }}</div>
                  <div class="student-name-logs-page">{{ formatStudentName(log.student_name) }}</div>
                </td>
                <td style="justify-content: center; align-items: center; text-align: center;">
                  <div class="time">{{ log.time }}</div>
                  <div class="date">{{ log.date }}</div>
                </td>
                <td style="justify-content: center; align-items: center; text-align: center;">
                  <div v-if="log.status === 'verified'" style="color: #3871c1; display: flex; align-items: center; gap: 8px;">
                    <div class="verified-circle-page">
                      <i class="fas fa-check"></i>
                    </div>
                    <span>Verified</span>
                  </div>
                  <div v-else style="color: #3871c1;">
                    <span>Unverified</span>
                  </div>
                </td>
                <td style="justify-content: center; align-items: center; text-align: center;">
                  <div style="color: black; font-weight: bold;">
                    {{ log.enrolled ? 'Enrolled' : 'Not Enrolled' }}
                  </div>
                  <div style="color: #8a8a8a;">
                    {{ log.has_id ? 'Presented ID' : 'No ID Presented' }}
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
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
  components: {
    Sidebar,
    TopBar
  },
  data() {
    return {
      logs: [], // Store logs fetched from the backend
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
          this.logs = result.data
            .map((log) => {
              const [date, time] = log.entry_time.split(" ");
              const formattedTime = time.slice(0, 5); // Remove seconds
              return { ...log, date, time: formattedTime };
            })
            .sort((a, b) => new Date(b.entry_time) - new Date(a.entry_time)); // Sort by entry_time (descending)
        } else {
          console.error("Failed to fetch logs:", result);
        }
      } catch (error) {
        console.error("Error fetching logs:", error);
      }
    },
    formatStudentName(name) {
      const parts = name.split(" ");
      const formattedParts = parts.map((part, index) => {
        if (index === parts.length - 1) {
          return part.charAt(0) + ".";
        } else {
          return part.charAt(0) + "*".repeat(part.length - 2) + part.charAt(part.length - 1);
        }
      });
      return formattedParts.join(" ");
    }
  },
  mounted() {
    this.fetchLogs(); // Fetch logs when the component is mounted
  },
};
</script>