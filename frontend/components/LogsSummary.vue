<template>
  <div class="logs-summary-container">
    <!-- Conditionally render these elements based on the minimalView prop -->
    <div v-if="!minimalView">
      <div class="logs-summary">
        Logs Summary
      </div>

      <div class="status-filter">
        Status:
      </div>

      <!-- Dropdown for Verified/Unverified -->
      <select class="status-dropdown" v-model="selectedStatus" @change="filterLogs">
        <option value="verified">Verified</option>
        <option value="unverified">Unverified</option>
        <option value="all">All</option>
      </select>

      <button class="print-button" @click="printLogs">
        <i class="fas fa-print"></i>
        Print
      </button>
    </div>

    <!-- Table -->
    <table class="logs-table">
      <thead>
        <tr>
          <th>Student No.</th>
          <th>Entry Time</th>
          <th>Status</th>
          <th>Remarks</th>
        </tr>
      </thead>
    </table>

    <div class="table-container-wrapper">
      <table class="info-table">
        <tbody>
          <!-- Render all rows in minimal view, otherwise limit to 5 -->
          <tr
            v-for="log in (minimalView ? filteredLogs : filteredLogs.slice(0, 5))"
            :key="log.id"
            style="border-bottom: 1px solid #e8e8e8;"
          >
            <td>
              <div class="student-number">{{ log.student_number }}</div>
              <div class="student-name-logs">{{ formatStudentName(log.student_name) }}</div>
            </td>
            <td style="justify-content: center; align-items: center; text-align: center;">
              <div class="time">{{ log.time }}</div>
              <div class="date">{{ log.date }}</div>
            </td>
            <td style="justify-content: center; align-items: center; text-align: center;">
              <div v-if="log.status === 'verified'" style="color: #3871c1; display: flex; align-items: center; gap: 8px;">
                <div class="verified-circle">
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

          <!-- Conditionally render "Show All Logs" only if not in minimal view -->
          <tr v-if="!minimalView">
            <td colspan="4" style="text-align: center; padding: 10px;">
              <div class="show-all" @click="navigateToLogsSummary" style="cursor: pointer; display: inline-block; text-align: center;">
                <span>Show All Logs</span>
                <div style="font-size: 20px; color: #3871c1;">&#x25BC;</div>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
// filepath: c:\Users\LJ\Desktop\Academics\2nd Sem 2024-2025\nuxt-laravel-app\frontend\components\LogsSummary.vue
import '~/assets/css/logs-summary.css';

export default {
  name: "LogsSummary",
  props: {
    minimalView: {
      type: Boolean,
      default: false, // Default to false to show all elements
    },
  },
  data() {
    return {
      logs: [], // Store logs fetched from the backend
      selectedStatus: "all",
      filteredLogs: [],
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

          this.filteredLogs = this.logs; // Initialize filtered logs
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
    },
    filterLogs() {
      if (this.selectedStatus === "all") {
        this.filteredLogs = this.logs;
      } else {
        this.filteredLogs = this.logs.filter((log) => log.status === this.selectedStatus);
      }
    },
    navigateToLogsSummary() {
      this.$router.push("/logs-summary-page");
    },
    printLogs() {
      window.print();
    },
  },
  mounted() {
    this.fetchLogs(); // Fetch logs when the component is mounted
  },
};
</script>