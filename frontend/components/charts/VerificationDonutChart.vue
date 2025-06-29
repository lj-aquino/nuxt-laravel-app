<template>
  <div class="chart-container">
    <canvas ref="donutChart"></canvas>
    <div class="legend">
      <div class="legend-item">
        <span class="color-box" style="background-color: #ff5757"></span>
        <span>Verified & Has ID</span>
      </div>
      <div class="legend-item">
        <span class="color-box" style="background-color: #8c52ff"></span>
        <span>Verified & No ID</span>
      </div>
      <div class="legend-item">
        <span class="color-box" style="background-color: #a6ffbd"></span>
        <span>Not Verified & Has ID</span>
      </div>
      <div class="legend-item">
        <span class="color-box" style="background-color: #ffbd59"></span>
        <span>Not Verified & No ID</span>
      </div>
    </div>
  </div>
</template>

<script>
import Chart from 'chart.js/auto'

export default {
  name: 'VerificationDonutChart',
  props: {
    logs: {
      type: Array,
      required: true
    }
  },
  data() {
    return {
      chart: null
    }
  },
  methods: {
    calculateData() {
      const data = {
        verifiedWithId: 0,
        verifiedNoId: 0,
        notVerifiedWithId: 0,
        notVerifiedNoId: 0
      }

      this.logs.forEach(log => {
        if (log.status === 'verified' && log.has_id) data.verifiedWithId++
        else if (log.status === 'verified' && !log.has_id) data.verifiedNoId++
        else if (log.status !== 'verified' && log.has_id) data.notVerifiedWithId++
        else data.notVerifiedNoId++
      })

      return Object.values(data)
    },
    createChart() {
      const ctx = this.$refs.donutChart.getContext('2d')
      
      this.chart = new Chart(ctx, {
        type: 'doughnut',
        data: {
          labels: [
            'Verified & Has ID',
            'Verified & No ID',
            'Not Verified & Has ID',
            'Not Verified & No ID'
          ],
          datasets: [{
            data: this.calculateData(),
            backgroundColor: ['#ff5757', '#8c52ff', '#a6ffbd', '#ffbd59'],
            borderWidth: 0
          }]
        },
        options: {
          responsive: true,
          plugins: {
            legend: {
              display: false
            }
          },
          cutout: '70%'
        }
      })
    }
  },
  mounted() {
    this.createChart()
  },
  watch: {
    logs: {
      handler() {
        if (this.chart) {
          this.chart.destroy()
        }
        this.createChart()
      },
      deep: true
    }
  }
}
</script>

<style scoped>
.chart-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 15px;
  width: calc(100% - 40px); /* Account for parent padding */
  height: calc(100% - 80px); /* Account for parent padding and title */
  position: absolute;
  top: 60px; /* Give space for title */
  left: 20px;
}

canvas {
  width: 100% !important;
  height: auto !important;
  max-width: 200px;
  max-height: 200px;
}

.legend {
  width: 100%;
  margin-top: 15px;
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 10px;
  padding: 0 20px;
}

.legend-item {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 12px;
}

.color-box {
  width: 12px;
  height: 12px;
  border-radius: 3px;
  flex-shrink: 0;
}
</style>