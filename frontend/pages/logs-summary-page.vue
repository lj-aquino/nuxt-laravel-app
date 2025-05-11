<template>
  <div class="logs-summary-page">
    <!-- Sidebar -->
    <Sidebar :activeMenu="'Logs Summary'" />
    <TopBar />

    <!-- Top Four Details -->
    <div class="top-details">
      <div class="total-entries-today" style="background-color: #e2ffed;">
        <div class="stat-title">Total Entries Today</div>
        <div class="stat-number">{{ todayStats.totalEntries }}</div>
        <div class="stat-subtitle">
          <span class="percentage" :class="{ positive: todayStats.entryPercentage > 0 }">
            {{ formatPercentage(todayStats.entryPercentage) }}
          </span>
          <span class="wtd">WTD: {{ weekStats.totalEntries }}</span>
        </div>
      </div>

      <div class="verified-entries" style="background-color: #fbffe0;">
        <div class="stat-title">Verified Entries</div>
        <div class="stat-number">{{ todayStats.verifiedEntries }}</div>
        <div class="stat-subtitle">
          <span class="percentage" :class="{ positive: todayStats.verifiedPercentage > 0 }">
            {{ formatPercentage(todayStats.verifiedPercentage) }}
          </span>
          <span class="wtd">WTD: {{ weekStats.verifiedEntries }}</span>
        </div>
      </div>

      <div class="presented-id" style="background-color: #eef6ff;">
        <div class="stat-title">Presented ID</div>
        <div class="stat-number">{{ todayStats.presentedId }}</div>
        <div class="stat-subtitle">
          <span class="percentage" :class="{ positive: todayStats.presentedIdPercentage > 0 }">
            {{ formatPercentage(todayStats.presentedIdPercentage) }}
          </span>
          <span class="wtd">WTD: {{ weekStats.presentedId }}</span>
        </div>
      </div>

      <div class="repeat-offenders" style="background-color: #ffefef;">
        <div class="stat-title">Repeat Offenders</div>
        <div class="stat-number">{{ weekStats.repeatOffenders }}</div>
        <div class="stat-subtitle">
          <span class="wtd">WTD: {{ weekStats.repeatOffendersTotal }}</span>
        </div>
      </div>
    </div>

    <div class="summary-plus-donut-chart">
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
                <div class="student-number-page">{{ getMaskedStudentNumber(log.student_number) }}</div>
                <div class="student-name-logs-page">{{ formatStudentName(log.student_name) }}</div>
              </td>
                <td style="justify-content: center; align-items: center; text-align: center;">
                  <div class="time">{{ log.time }}</div>
                  <div class="date">{{ log.date }}</div>
                </td>
                <td style="justify-content: center; align-items: center; text-align: center;">
                  <div v-if="log.status === 'verified'" style="color: #3871c1; display: flex; align-items: center; justify-content: center; width: 100%;">
                    <div class="verified-circle-page">
                      <i class="fas fa-check"></i>
                    </div>
                    <span style="margin-left: 8px;">Verified</span>
                  </div>
                  <div v-else style="color: #3871c1; display: flex; justify-content: center; width: 100%;">
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

      <div class="donut-chart">
        <div class = "donut-chart-header">
          <h3>Verification Breakdown</h3>
          <div class="verification-wtd">
            WTD
          </div>
        </div>
        <VerificationDonutChart :logs="logs" />
      </div>
    </div>
  </div>
</template>

<script>
import Sidebar from '~/components/Sidebar.vue';
import TopBar from '~/components/TopBar.vue';
import VerificationDonutChart from '~/components/charts/VerificationDonutChart.vue';
import '~/assets/css/logs-summary-page.css';
import { maskStudentNumber } from '~/utils/maskStudentNumber';

export default {
  components: {
    Sidebar,
    TopBar,
    VerificationDonutChart
  },
  data() {
    return {
      logs: [],
      todayStats: {
        totalEntries: 0,
        verifiedEntries: 0,
        presentedId: 0,
        entryPercentage: 0,
        verifiedPercentage: 0,
        presentedIdPercentage: 0,
      },
      weekStats: {
        totalEntries: 0,
        verifiedEntries: 0,
        presentedId: 0,
        repeatOffenders: 0,
        repeatOffendersTotal: 0,
      },
    };
  },
  methods: {
    async fetchLogs() {
      try {
        // Use runtime config to get API URL and key
        const config = useRuntimeConfig().public;
        const apiUrl = config.apiUrl;
        const apiKey = config.apiKey;
        
        const result = await fetch('https://sp-j16t.onrender.com/api/logs/recent', {
          method: 'GET',
          headers: {
            'Content-Type': 'application/json',
            'X-API-KEY': 'yFITiurVNg9eEXIReziZQQA4iHDlCaZSDxwUCpY9SAsMO36M6OIsRl2MErKBOn9q',
          },
        });
        
        const data = await result.json();
        
        if (data.success) {
          this.logs = data.data
            .map((log) => {
              const [date, time] = log.entry_time.split(" ");
              const formattedTime = time.slice(0, 5); // Remove seconds
              
              // Extract student properties from the nested student object
              const studentName = log.student ? log.student.student_name : 'N/A';
              const enrolled = log.student ? log.student.enrolled : false;
              
              return { 
                ...log, 
                date, 
                time: formattedTime,
                student_name: studentName,
                enrolled: enrolled
              };
            })
            .sort((a, b) => new Date(b.entry_time) - new Date(a.entry_time)); // Sort by entry_time (descending)
    
          // After fetching logs, calculate statistics
          this.calculateStats();
        } else {
          console.error("Failed to fetch logs:", data);
        }
      } catch (error) {
        console.error("Error fetching logs:", error);
      }
    },

    calculateStats() {
      const today = new Date();
      const yesterday = new Date(today);
      yesterday.setDate(yesterday.getDate() - 1);

      // Calculate today's stats
      const todayLogs = this.logs.filter(log => {
        const logDate = new Date(log.entry_time);
        return logDate.toDateString() === today.toDateString();
      });

      // Calculate yesterday's stats for percentage comparison
      const yesterdayLogs = this.logs.filter(log => {
        const logDate = new Date(log.entry_time);
        return logDate.toDateString() === yesterday.toDateString();
      });

      // Get week stats (Monday to Sunday)
      const currentWeekLogs = this.getWeekLogs();

      // Calculate repeat offenders
      const repeatOffenders = this.calculateRepeatOffenders(currentWeekLogs);

      this.todayStats = {
        totalEntries: todayLogs.length,
        verifiedEntries: todayLogs.filter(log => log.status === 'verified').length,
        presentedId: todayLogs.filter(log => log.has_id).length,
        entryPercentage: this.calculatePercentageChange(todayLogs.length, yesterdayLogs.length),
        verifiedPercentage: this.calculatePercentageChange(
          todayLogs.filter(log => log.status === 'verified').length,
          yesterdayLogs.filter(log => log.status === 'verified').length
        ),
        presentedIdPercentage: this.calculatePercentageChange(
          todayLogs.filter(log => log.has_id).length,
          yesterdayLogs.filter(log => log.has_id).length
        ),
      };

      this.weekStats = {
        totalEntries: currentWeekLogs.length,
        verifiedEntries: currentWeekLogs.filter(log => log.status === 'verified').length,
        presentedId: currentWeekLogs.filter(log => log.has_id).length,
        repeatOffenders: repeatOffenders.length,
        repeatOffendersTotal: this.calculateRepeatOffendersTotal(repeatOffenders),
      };
    },

    getWeekLogs() {
      const today = new Date();
      const monday = new Date(today);
      monday.setDate(today.getDate() - today.getDay() + (today.getDay() === 0 ? -6 : 1));
      monday.setHours(0, 0, 0, 0);

      return this.logs.filter(log => {
        const logDate = new Date(log.entry_time);
        return logDate >= monday && logDate <= today;
      });
    },

    calculateRepeatOffenders(weekLogs) {
      const studentCounts = {};
      weekLogs.forEach(log => {
        studentCounts[log.student_number] = (studentCounts[log.student_number] || 0) + 1;
      });
      return Object.entries(studentCounts)
        .filter(([_, count]) => count > 1)
        .map(([studentNumber]) => studentNumber);
    },

    calculateRepeatOffendersTotal(repeatOffenders) {
      return repeatOffenders.length;
    },

    calculatePercentageChange(current, previous) {
      if (previous === 0) return current > 0 ? 100 : 0;
      return ((current - previous) / previous) * 100;
    },

    formatPercentage(value) {
      return `${value >= 0 ? '+' : ''}${value.toFixed(0)}%`;
    },

    getMaskedStudentNumber(number) {
      return maskStudentNumber(number);
    },

    formatStudentName(name) {
      // Add null check to handle undefined or null student names
      if (!name) return 'N/A';
      
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
    this.fetchLogs().then(() => {
      this.calculateStats();
    });
  },
};
</script>