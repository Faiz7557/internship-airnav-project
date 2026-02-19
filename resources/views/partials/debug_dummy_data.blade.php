<script>
    // TEMP DEBUG: Inject Dummy Data if empty
    document.addEventListener("DOMContentLoaded", function() {
        if (!window.DashboardData) window.DashboardData = {};
        
        // Mock Heatmap Data
        if (!window.DashboardData.heatmapData || window.DashboardData.heatmapData.length === 0) {
            console.warn("Using Dummy Heatmap Data");
            const dummyHeatmap = [];
            const year = 2024;
            for (let m = 0; m < 12; m++) {
                for (let d = 1; d <= 28; d++) {
                    dummyHeatmap.push({
                        date: `${year}-${String(m+1).padStart(2,'0')}-${String(d).padStart(2,'0')}`,
                        value: Math.floor(Math.random() * 50) + 10,
                        year: year,
                        month: m + 1
                    });
                }
            }
            window.DashboardData.heatmapData = dummyHeatmap;
        }

        // Mock Peak Hour Data
        if (!window.DashboardData.peakHourfreq || Object.keys(window.DashboardData.peakHourfreq).length === 0) {
            console.warn("Using Dummy Peak Hour Data");
            window.DashboardData.peakHourfreq = {
                "08:00": 25, "09:00": 30, "10:00": 45, "11:00": 20, "12:00": 15
            };
        }

        // Mock Day of Week Data
        if (!window.DashboardData.dayOfWeekData || window.DashboardData.dayOfWeekData.length === 0) {
             console.warn("Using Dummy Day Data");
             window.DashboardData.dayOfWeekData = [120, 130, 110, 140, 150, 90, 80];
        }
    });
</script>
