
document.addEventListener("DOMContentLoaded", function () {
    // Access global data
    const data = window.DashboardData || {};

    // 1. REGISTER PLUGINS
    if (typeof ChartDataLabels !== 'undefined') Chart.register(ChartDataLabels);
    if (typeof ChartZoom !== 'undefined') Chart.register(ChartZoom);
    if (typeof window['chartjs-plugin-annotation'] !== 'undefined') Chart.register(window['chartjs-plugin-annotation']);

    // Define Global Variables for DrillDown
    const hourlyProfiles = data.hourlyProfiles || {};
    const dayOfWeekComposition = data.dayOfWeekComposition || {};
    const dayLabels = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
    const hourLabels = Array.from({ length: 24 }, (_, i) => String(i).padStart(2, '0') + ':00');

    let drillDownChartInstance = null;
    let modalCompositionChartInstance = null;

    // Custom Crosshair Plugin
    const crosshairPlugin = {
        id: 'crosshair',
        defaults: { width: 1, color: '#94a3b8', dash: [3, 3] },
        afterInit: (chart) => { chart.crosshair = { x: 0, y: 0 }; },
        afterEvent: (chart, args) => {
            const { inChartArea } = args;
            const { x, y } = args.event;
            chart.crosshair = { x, y, draw: inChartArea };
            chart.draw();
        },
        afterDatasetsDraw: (chart, args, options) => {
            const { ctx, chartArea: { top, bottom } } = chart;
            const { x, draw } = chart.crosshair;
            if (!draw) return;

            const activeElements = chart.getActiveElements();
            if (activeElements.length === 0) return;

            const lineX = activeElements[0].element.x;
            ctx.save();
            ctx.beginPath();
            ctx.lineWidth = options.width;
            ctx.strokeStyle = options.color;
            ctx.setLineDash(options.dash);
            ctx.moveTo(lineX, top);
            ctx.lineTo(lineX, bottom);
            ctx.stroke();
            ctx.restore();
        }
    };
    Chart.register(crosshairPlugin);

    // 2. COMMON CONFIG & HELPERS
    const COLOR_PRIMARY = '#1F3C88';
    const COLOR_SECONDARY = '#FDBE33';

    const createGradient = (ctx, colorStart, colorEnd) => {
        const gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, colorStart);
        gradient.addColorStop(1, colorEnd);
        return gradient;
    };

    const commonOptions = {
        responsive: true,
        maintainAspectRatio: false,
        interaction: { mode: 'index', intersect: false },
        hover: { mode: 'index', intersect: false },
        plugins: {
            legend: { labels: { font: { family: "'Poppins', sans-serif", size: 12 }, usePointStyle: true, boxWidth: 8, padding: 20 } },
            tooltip: {
                backgroundColor: 'rgba(255, 255, 255, 0.9)',
                titleColor: '#1e293b',
                bodyColor: '#64748b',
                borderColor: 'rgba(255, 255, 255, 0.5)',
                borderWidth: 1,
                padding: 12,
                cornerRadius: 16,
                displayColors: true,
                callbacks: { labelTextColor: function (context) { return '#64748b'; } }
            },
            datalabels: { display: false },
            zoom: {
                pan: { enabled: true, mode: 'x' },
                zoom: { wheel: { enabled: true }, pinch: { enabled: true }, mode: 'x' }
            },
            annotation: { annotations: {} }
        },
        scales: {
            y: { beginAtZero: true, grid: { color: 'rgba(226, 232, 240, 0.6)', borderDash: [4, 4] }, border: { display: false }, ticks: { font: { family: "'Outfit', sans-serif", size: 11 } } },
            x: { grid: { display: false }, border: { display: false }, ticks: { font: { family: "'Outfit', sans-serif", size: 11 } } }
        },
        elements: {
            line: { tension: 0.4 },
            point: { radius: 0, hitRadius: 30, hoverRadius: 8 },
            bar: { borderRadius: { topLeft: 8, topRight: 8 } }
        }
    };

    const withDataLabels = (options) => {
        return {
            ...options,
            plugins: {
                ...options.plugins,
                datalabels: {
                    display: 'auto',
                    align: 'top',
                    anchor: 'end',
                    offset: 4,
                    backgroundColor: 'rgba(255, 255, 255, 0.6)',
                    backdropBlur: 4,
                    borderRadius: 6,
                    color: '#475569',
                    font: { family: "'Outfit', sans-serif", size: 11, weight: 'bold' },
                    formatter: (value) => value > 0 ? value.toLocaleString() : '',
                    padding: 4
                }
            }
        };
    };

    // 3. INITIALIZE CHARTS

    // --- Trend Chart ---
    const ctxTrend = document.getElementById('trendChart');
    if (ctxTrend) {
        const ctx = ctxTrend.getContext('2d');
        const trendData = data.chartTrend || {};
        const events = data.events || [];
        const labelType = data.labelType;
        const currentMonth = data.month;
        const currentYear = data.year;

        const getLabelDate = (label, index) => {
            if (labelType === 'monthly') {
                return new Date(Date.parse("1 " + label));
            } else {
                return new Date(currentYear, currentMonth - 1, parseInt(label));
            }
        };

        const annotations = {};

        // Helper to Show Event Modal
        const showEventModal = (event, startIndex, endIndex) => {
            let totalVal = event.total || '-';
            let avgVal = event.avg || '-';
            let maxVal = event.peak || '-';
            let peakDateLabel = event.peakDate || '-';

            const opts = { day: 'numeric', month: 'long', year: 'numeric' };
            const startStr = new Date(event.start).toLocaleDateString('id-ID', opts);
            const endStr = new Date(event.end).toLocaleDateString('id-ID', opts);

            const elTitle = document.getElementById('eventModalTitle');
            if (elTitle) elTitle.innerText = event.name;
            const elDate = document.getElementById('eventModalDate');
            if (elDate) elDate.innerText = `${startStr} - ${endStr}`;

            const elTotal = document.getElementById('eventModalTotal');
            if (elTotal) elTotal.innerText = totalVal;
            const elAvg = document.getElementById('eventModalAvg');
            if (elAvg) elAvg.innerText = avgVal;

            const elPeakDate = document.getElementById('eventModalPeakDate');
            if (elPeakDate) elPeakDate.innerText = peakDateLabel;
            const elPeakVal = document.getElementById('eventModalPeakVal');
            if (elPeakVal) elPeakVal.innerText = maxVal;

            const modal = document.getElementById('eventDetailModal');
            if (modal) {
                const deleteForm = document.getElementById('deleteEventForm');
                if (deleteForm && event.id) {
                    deleteForm.action = `/dashboard/events/${event.id}`;
                    deleteForm.classList.remove('hidden');
                } else if (deleteForm) {
                    deleteForm.classList.add('hidden');
                }
                modal.classList.remove('hidden');
            }
        };

        const closeEventModal = () => {
            const m = document.getElementById('eventDetailModal');
            if (m) m.classList.add('hidden');
        };

        const btnCloseEvent = document.getElementById('closeEventModalBtn');
        if (btnCloseEvent) btnCloseEvent.addEventListener('click', closeEventModal);
        const btnCloseEventText = document.getElementById('closeEventModalBtnText');
        if (btnCloseEventText) btnCloseEventText.addEventListener('click', closeEventModal);
        const eventBackdrop = document.getElementById('eventModalBackdrop');
        if (eventBackdrop) eventBackdrop.addEventListener('click', closeEventModal);


        if (events.length > 0 && trendData.labels && trendData.labels.length > 0) {
            const chartDates = trendData.labels.map((lbl, idx) => ({
                index: idx,
                date: getLabelDate(lbl, idx)
            }));

            events.forEach((event, idx) => {
                const evtStart = new Date(event.start);
                const evtEnd = new Date(event.end);

                const firstDate = chartDates[0].date;
                const lastDate = chartDates[chartDates.length - 1].date;

                if (evtEnd < firstDate || evtStart > lastDate) return;

                let startIndex = -1;
                let endIndex = -1;

                if (labelType === 'monthly') {
                    const matchStart = chartDates.find(d =>
                        d.date.getFullYear() === evtStart.getFullYear() &&
                        d.date.getMonth() === evtStart.getMonth()
                    );
                    const matchEnd = chartDates.find(d =>
                        d.date.getFullYear() === evtEnd.getFullYear() &&
                        d.date.getMonth() === evtEnd.getMonth()
                    );

                    if (matchStart) startIndex = matchStart.index;
                    if (matchEnd) endIndex = matchEnd.index;
                } else {
                    const matches = chartDates.filter(d => d.date >= evtStart && d.date <= evtEnd);
                    if (matches.length > 0) {
                        startIndex = matches[0].index;
                        endIndex = matches[matches.length - 1].index;
                    }
                }

                if (startIndex !== -1) {
                    if (endIndex === -1) endIndex = chartDates[chartDates.length - 1].index;

                    annotations['event' + idx] = {
                        type: 'box',
                        xMin: startIndex - 0.4,
                        xMax: endIndex + 0.4,
                        backgroundColor: event.color,
                        borderColor: event.borderColor,
                        borderWidth: 1,
                        label: {
                            display: true,
                            content: event.name + " (Klik Detail)",
                            position: 'start',
                            color: '#64748b',
                            font: { size: 10, weight: 'bold' },
                            yAdjust: -20
                        },
                        click: function (context) {
                            showEventModal(event, startIndex, endIndex);
                        },
                        enter: function (context) {
                            context.chart.canvas.style.cursor = 'pointer';
                        },
                        leave: function (context) {
                            context.chart.canvas.style.cursor = 'default';
                        }
                    };
                }
            });
        }

        const trendChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: trendData.labels,
                datasets: [
                    { label: 'Total Flights', data: trendData.data || [], borderColor: COLOR_PRIMARY, backgroundColor: createGradient(ctx, 'rgba(31, 60, 136, 0.2)', 'rgba(31, 60, 136, 0.0)'), borderWidth: 3, fill: true, pointBackgroundColor: '#fff', pointBorderColor: COLOR_PRIMARY, pointBorderWidth: 2 },
                    { label: 'Domestik', data: trendData.dom || [], borderColor: '#10b981', backgroundColor: createGradient(ctx, 'rgba(16, 185, 129, 0.2)', 'rgba(16, 185, 129, 0.0)'), borderWidth: 2, fill: true, pointBackgroundColor: '#fff', pointBorderColor: '#10b981', hidden: true },
                    { label: 'Internasional', data: trendData.int || [], borderColor: '#f59e0b', backgroundColor: createGradient(ctx, 'rgba(245, 158, 11, 0.2)', 'rgba(245, 158, 11, 0.0)'), borderWidth: 2, fill: true, pointBackgroundColor: '#fff', pointBorderColor: '#f59e0b', hidden: true },
                    { label: 'Training', data: trendData.training || [], borderColor: '#64748b', backgroundColor: createGradient(ctx, 'rgba(100, 116, 139, 0.2)', 'rgba(100, 116, 139, 0.0)'), borderWidth: 2, fill: true, pointBackgroundColor: '#fff', pointBorderColor: '#64748b', hidden: true }
                ]
            },
            options: withDataLabels({
                ...commonOptions,
                plugins: {
                    ...commonOptions.plugins,
                    legend: { display: false },
                    annotation: { annotations: annotations }
                },
                interaction: { mode: 'index', intersect: false }
            })
        });

        const trendButtons = ['total', 'dom', 'int', 'train'];
        trendButtons.forEach((type, index) => {
            const btn = document.getElementById(`btn-trend-${type}`);
            if (btn) {
                btn.addEventListener('click', () => {
                    trendChart.data.datasets.forEach((ds, i) => trendChart.setDatasetVisibility(i, i === index));
                    trendChart.update();
                    trendButtons.forEach(t => {
                        const b = document.getElementById(`btn-trend-${t}`);
                        if (b) { b.classList.remove('active'); b.classList.add('inactive'); }
                    });
                    btn.classList.remove('inactive');
                    btn.classList.add('active');
                });
            }
        });
    }

    // --- Arr vs Dep Chart ---
    const ctxArrDep = document.getElementById('arrDepChart');
    if (ctxArrDep) {
        const adData = data.chartArrDep || {};
        const arrDepChart = new Chart(ctxArrDep.getContext('2d'), {
            type: 'line',
            data: {
                labels: (data.chartTrend || {}).labels || [],
                datasets: [
                    { label: 'Arrival', data: adData.arr, borderColor: '#10b981', backgroundColor: 'rgba(16, 185, 129, 0.1)', borderWidth: 2, fill: true, pointBackgroundColor: '#fff', pointBorderColor: '#10b981', tension: 0.4 },
                    { label: 'Departure', data: adData.dep, borderColor: '#0ea5e9', backgroundColor: 'rgba(14, 165, 233, 0.1)', borderWidth: 2, borderDash: [5, 5], fill: true, pointBackgroundColor: '#fff', pointBorderColor: '#0ea5e9', tension: 0.4 }
                ]
            },
            options: withDataLabels({
                ...commonOptions,
                plugins: { ...commonOptions.plugins, legend: { position: 'top', align: 'end' } },
                interaction: { mode: 'index', intersect: false }
            })
        });

        const adButtons = { 'all': [true, true], 'arr': [true, false], 'dep': [false, true] };
        Object.keys(adButtons).forEach(key => {
            const btn = document.getElementById(`btn-${key}`);
            if (btn) {
                btn.addEventListener('click', () => {
                    arrDepChart.setDatasetVisibility(0, adButtons[key][0]);
                    arrDepChart.setDatasetVisibility(1, adButtons[key][1]);
                    arrDepChart.update();
                    Object.keys(adButtons).forEach(k => {
                        const b = document.getElementById(`btn-${k}`);
                        if (b) { b.classList.remove('active'); b.classList.add('inactive'); }
                    });
                    btn.classList.remove('inactive');
                    btn.classList.add('active');
                });
            }
        });
    }

    // --- Category Chart (Donut) ---
    const ctxCategory = document.getElementById('categoryChart');
    if (ctxCategory) {
        const catData = data.chartCategory || {};
        new Chart(ctxCategory.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: ['Domestik', 'Internasional', 'Training'],
                datasets: [{
                    data: [catData.dom, catData.int, catData.training],
                    backgroundColor: [COLOR_PRIMARY, COLOR_SECONDARY, '#cbd5e1'],
                    borderWidth: 0, hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '75%',
                plugins: { legend: { display: false }, datalabels: { display: false }, zoom: { pan: { enabled: false }, zoom: { wheel: { enabled: false } } } }
            }
        });
    }

    // --- Peak Hour Chart ---
    const ctxPeak = document.getElementById('peakChart');
    if (ctxPeak) {
        const peakData = data.peakHourfreq || {};
        const labels = Object.keys(peakData);
        const values = Object.values(peakData);

        const maxPeakVal = values.length ? Math.max(...values) : 1;
        const peakThreshold = maxPeakVal * 0.08; // only label bars > 8% of max

        new Chart(ctxPeak, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Frekuensi',
                    data: values,
                    backgroundColor: (ctx) => {
                        const v = ctx.dataset.data[ctx.dataIndex];
                        const opacity = 0.35 + 0.65 * (v / maxPeakVal);
                        return `rgba(244, 63, 94, ${opacity.toFixed(2)})`;
                    },
                    borderRadius: 6,
                    clip: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                layout: { padding: { top: 28, left: 4, right: 4, bottom: 4 } },
                plugins: {
                    legend: { display: false },
                    datalabels: {
                        color: '#64748b',
                        anchor: 'end',
                        align: 'top',
                        display: (c) => (c.dataset.data[c.dataIndex] || 0) >= peakThreshold,
                        font: { family: "'Outfit', sans-serif", size: 9, weight: 'bold' },
                        clamp: false
                    }
                },
                scales: {
                    y: {
                        display: true,
                        beginAtZero: true,
                        max: Math.ceil(maxPeakVal * 1.1 / 50) * 50,
                        border: { display: false },
                        grid: { color: 'rgba(148,163,184,0.12)', drawTicks: false },
                        ticks: {
                            font: { family: "'Outfit', sans-serif", size: 9 },
                            color: '#b0bec5',
                            padding: 4,
                            maxTicksLimit: 4,
                            callback: (v) => v === 0 ? '' : v
                        }
                    },
                    x: {
                        display: true,
                        grid: { display: false },
                        border: { display: false },
                        ticks: {
                            font: { family: "'Outfit', sans-serif", size: 9, weight: '500' },
                            color: '#94a3b8',
                            maxRotation: 0,
                            callback: (val, i) => i % 2 === 0 ? labels[i] : ''
                        }
                    }
                }
            }
        });
    }

    // --- Day of Week Chart (With Drill-Down) ---
    const ctxDay = document.getElementById('dayOfWeekChart');
    if (ctxDay) {
        const dayValues = data.dayOfWeekData || [];

        const minDayVal = dayValues.length ? Math.min(...dayValues) : 0;
        const maxDayVal = dayValues.length ? Math.max(...dayValues) : 1;
        const daySpread = maxDayVal - minDayVal;
        const yMin = Math.max(0, Math.floor(minDayVal - daySpread * 0.8));
        const yMax = Math.ceil(maxDayVal + daySpread * 0.5);

        new Chart(ctxDay, {
            type: 'bar',
            data: {
                labels: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
                datasets: [{
                    label: 'Avg Flights',
                    data: dayValues,
                    backgroundColor: (ctx) => {
                        // Highlight weekend bars
                        return ctx.dataIndex >= 5 ? 'rgba(99, 102, 241, 0.75)' : 'rgba(31, 60, 136, 0.82)';
                    },
                    borderRadius: 8,
                    hoverBackgroundColor: COLOR_SECONDARY,
                    clip: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                layout: { padding: { top: 30 } },
                onHover: (event, chartElement) => {
                    event.native.target.style.cursor = chartElement[0] ? 'pointer' : 'default';
                },
                onClick: (event, elements) => {
                    if (elements.length > 0) {
                        openDrillDown(elements[0].index);
                    }
                },
                plugins: {
                    legend: { display: false },
                    datalabels: {
                        color: '#475569',
                        anchor: 'end',
                        align: 'top',
                        display: (c) => (c.dataset.data[c.dataIndex] || 0) > 0,
                        font: { family: "'Outfit', sans-serif", size: 11, weight: 'bold' },
                        formatter: (value) => Math.round(value).toLocaleString(),
                        clamp: false
                    }
                },
                scales: {
                    y: {
                        display: true,
                        min: yMin,
                        max: yMax,
                        border: { display: false },
                        grid: { color: 'rgba(148,163,184,0.12)', drawTicks: false },
                        ticks: {
                            font: { family: "'Outfit', sans-serif", size: 10 },
                            color: '#b0bec5',
                            padding: 4,
                            maxTicksLimit: 4,
                            callback: (v) => v.toLocaleString()
                        }
                    },
                    x: {
                        display: true,
                        grid: { display: false },
                        border: { display: false },
                        ticks: {
                            font: { family: "'Outfit', sans-serif", size: 11, weight: '600' },
                            color: '#94a3b8'
                        }
                    }
                }
            }
        });
    }

    // Drill Down Modal Logic (ENHANCED)
    const modal = document.getElementById('drillDownModal');
    const modalBackdrop = document.getElementById('modalBackdrop');
    const modalPanel = document.getElementById('modalPanel');
    const closeModalBtns = [document.getElementById('closeModalBtn'), document.getElementById('closeModalBtnText')];

    function openDrillDown(dayIndex) {
        modal.classList.remove('hidden');
        const dayName = dayLabels[dayIndex];
        document.getElementById('modalDayName').innerText = dayName;

        const profileData = hourlyProfiles[dayIndex + 1] || [];

        const totalDaily = profileData.reduce((a, b) => a + b, 0);
        const peakValue = Math.max(...profileData);
        const peakHourIndex = profileData.indexOf(peakValue);
        const peakHourFmt = hourLabels[peakHourIndex] || '-';

        document.getElementById('modalTotalFlights').innerText = totalDaily.toLocaleString();
        document.getElementById('modalPeakHour').innerText = peakHourFmt;

        const avgDaily = data.avgDailyFlights || 0;
        let status = 'Normal';
        let statusColor = 'text-emerald-600';
        if (totalDaily > avgDaily * 1.1) { status = 'Tinggi'; statusColor = 'text-rose-600'; }
        else if (totalDaily < avgDaily * 0.9) { status = 'Rendah'; statusColor = 'text-slate-500'; }

        const statusEl = document.getElementById('modalStatus');
        statusEl.innerText = status;
        statusEl.className = `text-lg font-bold ${statusColor}`;

        const calcSum = (start, end) => profileData.slice(start, end).reduce((a, b) => a + b, 0);

        const volMalam = calcSum(0, 6);
        const volPagi = calcSum(6, 12);
        const volSiang = calcSum(12, 18);
        const volSore = calcSum(18, 24);

        const updateBar = (idVal, idBar, val, total) => {
            const pct = total > 0 ? (val / total) * 100 : 0;
            document.getElementById(idVal).innerText = `${val} (${Math.round(pct)}%)`;
            document.getElementById(idBar).style.width = `${pct}%`;
        };

        updateBar('statPagiVal', 'statPagiBar', volPagi, totalDaily);
        updateBar('statSiangVal', 'statSiangBar', volSiang, totalDaily);
        updateBar('statSoreVal', 'statSoreBar', volSore, totalDaily);
        updateBar('statMalamVal', 'statMalamBar', volMalam, totalDaily);

        const top3 = profileData
            .map((val, idx) => ({ hour: hourLabels[idx], val }))
            .sort((a, b) => b.val - a.val)
            .slice(0, 3);

        const top3HTML = top3.map((item, i) =>
            `<div class="flex justify-between items-center py-2 border-b border-slate-200 last:border-0 border-r last:border-r-0 pr-4 last:pr-0 border-indigo-100">
                <div class="flex items-center gap-2">
                    <span class="w-6 h-6 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-xs font-bold">${i + 1}</span>
                    <span class="text-slate-600 font-medium text-xs">${item.hour}</span>
                </div>
                <span class="font-bold text-slate-800 text-sm">${item.val} <span class="text-[10px] font-normal text-slate-400">Pnb</span></span>
            </div>`
        ).join('');
        const listContainer = document.getElementById('modalTop3List');
        if (listContainer) listContainer.innerHTML = top3HTML;

        const periods = [
            { name: 'Pagi', val: volPagi },
            { name: 'Siang', val: volSiang },
            { name: 'Sore', val: volSore },
            { name: 'Malam', val: volMalam }
        ];
        const maxPeriod = periods.reduce((prev, current) => (prev.val > current.val) ? prev : current);
        const insightHTML = `Trafik terpadat terjadi pada <span class="font-bold text-slate-700">${maxPeriod.name} hari</span> dengan total ${maxPeriod.val} pergerakan.`;
        document.getElementById('modalInsightText').innerHTML = insightHTML;

        const ctxDD = document.getElementById('drillDownChart').getContext('2d');
        if (drillDownChartInstance) drillDownChartInstance.destroy();

        const gradientDD = createGradient(ctxDD, 'rgba(99, 102, 241, 0.5)', 'rgba(99, 102, 241, 0.0)');

        drillDownChartInstance = new Chart(ctxDD, {
            type: 'line',
            data: {
                labels: hourLabels,
                datasets: [{
                    label: 'Pergerakan',
                    data: profileData,
                    borderColor: '#4f46e5', backgroundColor: gradientDD, borderWidth: 3, fill: true, pointBackgroundColor: '#fff', pointBorderColor: '#4f46e5', pointRadius: 4, tension: 0.4
                }]
            },
            options: {
                ...commonOptions,
                maintainAspectRatio: false,
                plugins: { legend: { display: false }, zoom: { pan: { enabled: false }, zoom: { wheel: { enabled: false } } } },
                scales: { y: { beginAtZero: true, grid: { color: 'rgba(226, 232, 240, 0.5)' } }, x: { grid: { display: false }, ticks: { maxTicksLimit: 8 } } }
            }
        });

        const domVal = dayOfWeekComposition?.dom?.[dayIndex + 1] || 0;
        const intVal = dayOfWeekComposition?.int?.[dayIndex + 1] || 0;
        const trainVal = dayOfWeekComposition?.training?.[dayIndex + 1] || 0;
        const totalComp = domVal + intVal + trainVal;

        const totalEl = document.getElementById('modalCompTotal');
        if (totalEl) totalEl.innerText = Math.round(totalComp).toLocaleString();

        const ctxComp = document.getElementById('modalCompositionChart');
        if (ctxComp) {
            if (modalCompositionChartInstance) modalCompositionChartInstance.destroy();
            modalCompositionChartInstance = new Chart(ctxComp.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: ['Domestik', 'Internasional', 'Training'],
                    datasets: [{
                        data: [domVal, intVal, trainVal],
                        backgroundColor: ['#10b981', '#f59e0b', '#94a3b8'],
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '75%',
                    plugins: { legend: { display: false }, datalabels: { display: false }, tooltip: { callbacks: { label: (c) => ` ${c.label}: ${Math.round(c.raw)}` } } }
                }
            });
        }

        void modal.offsetWidth;
        modalBackdrop.classList.remove('opacity-0');
        modalPanel.classList.remove('scale-95', 'opacity-0');
        modalPanel.classList.add('scale-100', 'opacity-100');
    }

    function closeModal() {
        modalBackdrop.classList.add('opacity-0');
        modalPanel.classList.remove('scale-100', 'opacity-100');
        modalPanel.classList.add('scale-95', 'opacity-0');
        setTimeout(() => { modal.classList.add('hidden'); }, 300);
    }
    if (closeModalBtns) closeModalBtns.forEach(btn => { if (btn) btn.addEventListener('click', closeModal); });
    if (modalBackdrop) modalBackdrop.addEventListener('click', closeModal);

    // --- Yearly Comparison Chart (Aggregated/Zoomable) ---
    if (data.yearlyComparison) {
        const ctxYearly = document.getElementById('yearlyChart');
        if (ctxYearly) {
            // Add Zoom & Pan Controls next to title
            const chartCardHeader = ctxYearly.closest('.glass-card').querySelector('h3').parentElement;
            if (!document.getElementById('zoomControls')) {
                const controlsHtml = `
                    <div id="zoomControls" class="flex items-center gap-1.5 ml-auto bg-white/80 backdrop-blur-sm p-1.5 rounded-2xl border border-slate-200/60 shadow-sm">
                        <div class="flex gap-1">
                            <button id="panLeftBtn" class="flex items-center justify-center w-7 h-7 rounded-lg bg-slate-50 text-slate-500 hover:bg-white hover:text-indigo-600 transition-all duration-300 shadow-sm border border-slate-200 hover:border-indigo-200 hover:shadow-indigo-100" title="Geser Kiri">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                            </button>
                            <button id="panRightBtn" class="flex items-center justify-center w-7 h-7 rounded-lg bg-slate-50 text-slate-500 hover:bg-white hover:text-indigo-600 transition-all duration-300 shadow-sm border border-slate-200 hover:border-indigo-200 hover:shadow-indigo-100" title="Geser Kanan">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                            </button>
                        </div>
                        <div class="w-px h-5 bg-slate-200 self-center mx-1"></div>
                        <div class="flex gap-1">
                            <button id="zoomOutBtn" class="flex items-center justify-center w-7 h-7 rounded-lg bg-slate-50 text-slate-500 hover:bg-white hover:text-indigo-600 transition-all duration-300 shadow-sm border border-slate-200 hover:border-indigo-200 hover:shadow-indigo-100" title="Zoom Out">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" /></svg>
                            </button>
                            <button id="zoomInBtn" class="flex items-center justify-center w-7 h-7 rounded-lg bg-slate-50 text-slate-500 hover:bg-white hover:text-indigo-600 transition-all duration-300 shadow-sm border border-slate-200 hover:border-indigo-200 hover:shadow-indigo-100" title="Zoom In">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                            </button>
                        </div>
                        <button id="resetZoomBtn" class="hidden flex items-center justify-center px-3 h-7 rounded-lg bg-indigo-50 text-indigo-700 font-bold text-[10px] uppercase tracking-wider hover:bg-indigo-600 hover:text-white transition-all duration-300 shadow-sm border border-indigo-200 hover:border-indigo-600 ml-1 group" title="Reset Default">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1.5 text-indigo-500 group-hover:text-white transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                            Reset
                        </button>
                    </div>`;
                chartCardHeader.insertAdjacentHTML('beforeend', controlsHtml);
            }
            const resetBtn = document.getElementById('resetZoomBtn');
            const zoomInBtn = document.getElementById('zoomInBtn');
            const zoomOutBtn = document.getElementById('zoomOutBtn');
            const panLeftBtn = document.getElementById('panLeftBtn');
            const panRightBtn = document.getElementById('panRightBtn');

            const yearlyData = data.yearlyComparison; // format: { '2023': [{x: 1, y: 100}, {x:2, y:120}...], '2024': ... }
            const colors = { '2023': '#cbd5e1', '2024': '#94a3b8', '2025': COLOR_SECONDARY, '2026': COLOR_PRIMARY };

            const datasets = Object.keys(yearlyData).map(year => {
                // To make the line smooth but not crazy, we apply a moving average
                const rawData = yearlyData[year];
                // simple 7-day moving average to smooth the violent daily spikes when fully zoomed out
                const smoothedData = rawData.map((pt, idx, arr) => {
                    let sum = 0; let count = 0;
                    for (let i = Math.max(0, idx - 3); i <= Math.min(arr.length - 1, idx + 3); i++) {
                        sum += arr[i].y; count++;
                    }
                    return { x: pt.x, y: sum / count, rawY: pt.y };
                });

                return {
                    label: year,
                    data: smoothedData,
                    borderColor: colors[year] || '#ef4444',
                    borderWidth: year === '2026' ? 3 : 2,
                    borderDash: year === '2026' || year === '2025' ? [] : [4, 4],
                    pointRadius: 0,
                    pointHoverRadius: 5,
                    tension: 0.3,
                    parsing: { xAxisKey: 'x', yAxisKey: 'y' }
                };
            });

            // Helper to get Date string from day of year
            const getDateFromDayOfYear = (day) => {
                const leapYear = new Date().getFullYear(); // Use current year just for label mapping
                const d = new Date(leapYear, 0); // Jan 1
                d.setDate(day); // Sets the day of the year
                return d;
            };

            const yearlyChartInstance = new Chart(ctxYearly.getContext('2d'), {
                type: 'line',
                data: { datasets: datasets },
                options: {
                    ...commonOptions,
                    plugins: {
                        ...commonOptions.plugins,
                        legend: { position: 'top', align: 'end' },
                        zoom: {
                            limits: {
                                x: { min: 1, max: 366 }
                            },
                            pan: {
                                enabled: false,
                                mode: 'x',
                                onPanComplete({ chart }) {
                                    const min = chart.scales.x.min;
                                    const max = chart.scales.x.max;
                                    if (max - min < 360) resetBtn.classList.remove('hidden');
                                    else resetBtn.classList.add('hidden');
                                }
                            },
                            zoom: {
                                wheel: { enabled: false },
                                pinch: { enabled: false },
                                mode: 'x',
                                onZoomComplete({ chart }) {
                                    const min = chart.scales.x.min;
                                    const max = chart.scales.x.max;
                                    // if zoomed in, show reset button
                                    if (max - min < 360) resetBtn.classList.remove('hidden');
                                    else resetBtn.classList.add('hidden');
                                }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                title: (context) => {
                                    const dayNum = context[0].parsed.x;
                                    const dateObj = getDateFromDayOfYear(dayNum);
                                    return dateObj.toLocaleDateString('id-ID', { day: 'numeric', month: 'long' });
                                },
                                label: (c) => {
                                    const rawTarget = c.raw.rawY; // the original non-smoothed data
                                    return ` ${c.dataset.label}: ${Math.round(rawTarget).toLocaleString()}`;
                                }
                            }
                        }
                    },
                    interaction: { mode: 'index', intersect: false },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(226, 232, 240, 0.6)', borderDash: [4, 4] },
                            border: { display: false }
                        },
                        x: {
                            type: 'linear',
                            min: 1,
                            max: 366,
                            bounds: 'ticks',
                            grid: { display: false },
                            border: { display: false },
                            afterBuildTicks: function (axis) {
                                const range = axis.max - axis.min;
                                if (range > 180) {
                                    const monthStartDays = [1, 32, 61, 92, 122, 153, 183, 214, 245, 275, 306, 336];
                                    axis.ticks = monthStartDays.filter(d => d >= axis.min && d <= axis.max).map(d => ({ value: d }));
                                }
                            },
                            ticks: {
                                callback: function (value, index, ticks) {
                                    if (value < 1 || value > 366) return null;
                                    const range = this.max - this.min;
                                    const d = getDateFromDayOfYear(value);

                                    if (range > 180) {
                                        return d.toLocaleDateString('id-ID', { month: 'short' });
                                    } else {
                                        return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
                                    }
                                },
                                maxRotation: 0,
                                autoSkip: false,
                                maxTicksLimit: 12
                            }
                        }
                    }
                }
            });

            resetBtn.addEventListener('click', () => {
                yearlyChartInstance.resetZoom();
                resetBtn.classList.add('hidden');
            });
            zoomInBtn.addEventListener('click', () => {
                yearlyChartInstance.zoom(1.2);
                const min = yearlyChartInstance.scales.x.min;
                const max = yearlyChartInstance.scales.x.max;
                if (max - min < 360) resetBtn.classList.remove('hidden');
            });
            zoomOutBtn.addEventListener('click', () => {
                yearlyChartInstance.zoom(0.8);
                const min = yearlyChartInstance.scales.x.min;
                const max = yearlyChartInstance.scales.x.max;
                if (max - min >= 360) resetBtn.classList.add('hidden');
                else resetBtn.classList.remove('hidden');
            });
            panLeftBtn.addEventListener('click', () => {
                yearlyChartInstance.pan({ x: 50 });
            });
            panRightBtn.addEventListener('click', () => {
                yearlyChartInstance.pan({ x: -50 });
            });
        }
    }

    // --- Helper: Update KPI Cards (Client-Side) ---
    const updateSeasonalStats = (currYear, prevYear, allData) => {
        const currData = allData.filter(d => d.year == currYear);
        if (currData.length === 0) return;

        const maxDateStr = currData.reduce((max, p) => p.date > max ? p.date : max, "0000-00-00");
        const getDayId = (dStr) => {
            const d = new Date(dStr);
            return d.getMonth() * 100 + d.getDate();
        };
        const cutoffId = getDayId(maxDateStr);

        const prevData = allData.filter(d => {
            if (d.year != prevYear) return false;
            const dId = getDayId(d.date);
            return dId <= cutoffId;
        });

        const calcStats = (arr) => {
            if (arr.length === 0) return { total: 0, peak: 0, avg: 0 };
            const total = arr.reduce((a, b) => a + b.value, 0);
            const peak = Math.max(...arr.map(d => d.value));
            const avg = Math.round(total / arr.length);
            return { total, peak, avg };
        };

        const currStats = calcStats(currData);
        const prevStats = calcStats(prevData);

        const calcGrowth = (curr, prev) => {
            if (prev === 0) return 100;
            return Math.round(((curr - prev) / prev) * 100 * 10) / 10;
        };

        const totalGrowth = calcGrowth(currStats.total, prevStats.total);
        const peakGrowth = calcGrowth(currStats.peak, prevStats.peak);
        const avgGrowth = calcGrowth(currStats.avg, prevStats.avg);

        const updateCard = (idVal, idIcon, idText, idContainer, idVs, val, growth, prevYearLbl) => {
            const elVal = document.getElementById(idVal);
            if (elVal) elVal.innerText = val.toLocaleString();

            const elText = document.getElementById(idText);
            if (elText) elText.innerText = Math.abs(growth) + '%';

            const elVs = document.getElementById(idVs);
            if (elVs) {
                // If it's a MoM card, the label might already contain 'vs', so we can just use the provided text
                elVs.innerText = prevYearLbl.toString().startsWith('vs') ? prevYearLbl : 'vs ' + prevYearLbl;
            }

            const container = document.getElementById(idContainer);
            const iconSpan = document.getElementById(idIcon);

            if (container) container.className = `flex items-center gap-1.5 text-xs font-bold px-2.5 py-1 rounded-full w-fit mt-2 ${growth >= 0 ? 'text-emerald-600 bg-emerald-50' : 'text-rose-600 bg-rose-50'}`;

            const upIcon = `<svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd" /></svg>`;
            const downIcon = `<svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>`;

            if (iconSpan) iconSpan.innerHTML = growth >= 0 ? upIcon : downIcon;
        };

        updateCard('kpiTotalVal', 'kpiTotalGrowthIcon', 'kpiTotalGrowthText', 'kpiTotalGrowthContainer', 'kpiTotalVs', currStats.total, totalGrowth, prevYear);
        updateCard('kpiPeakVal', 'kpiPeakGrowthIcon', 'kpiPeakGrowthText', 'kpiPeakGrowthContainer', 'kpiPeakVs', currStats.peak, peakGrowth, prevYear);
        updateCard('kpiAvgVal', 'kpiAvgGrowthIcon', 'kpiAvgGrowthText', 'kpiAvgGrowthContainer', 'kpiAvgVs', currStats.avg, avgGrowth, prevYear);

        // MoM calculations
        let currMonthData = [];
        let prevMonthData = [];
        let momSubtitle = 'vs Last Month';

        if (currData.length > 0 && maxDateStr !== "0000-00-00") {
            const maxD = new Date(maxDateStr);
            const maxMonth = maxD.getMonth() + 1; // 1-12
            const maxYear = maxD.getFullYear();

            let prevMonth = maxMonth - 1;
            let prevMonthYear = maxYear;
            if (prevMonth === 0) {
                prevMonth = 12;
                prevMonthYear = maxYear - 1;
            }

            currMonthData = allData.filter(d => d.year == maxYear && d.month == maxMonth);
            prevMonthData = allData.filter(d => d.year == prevMonthYear && d.month == prevMonth);

            const monthNames = ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agt", "Sep", "Okt", "Nov", "Des"];
            momSubtitle = `vs ${monthNames[prevMonth - 1]} ${prevMonthYear !== maxYear ? prevMonthYear : ''}`;
        }

        const calcMomStats = (arr) => {
            if (arr.length === 0) return { peakHour: 0, avgDaily: 0 };
            const peakHour = Math.max(...arr.map(d => d.peak || 0));
            const avgDaily = Math.round(arr.reduce((a, b) => a + b.value, 0) / arr.length);
            return { peakHour, avgDaily };
        };

        const currMom = calcMomStats(currMonthData);
        const prevMom = calcMomStats(prevMonthData);

        const peakHourGrowth = calcGrowth(currMom.peakHour, prevMom.peakHour);
        const avgDailyGrowth = calcGrowth(currMom.avgDaily, prevMom.avgDaily);

        updateCard('kpiMomPeakVal', 'kpiMomPeakGrowthIcon', 'kpiMomPeakGrowthText', 'kpiMomPeakGrowthContainer', 'kpiMomPeakVs', currMom.peakHour, peakHourGrowth, momSubtitle);
        updateCard('kpiMomAvgVal', 'kpiMomAvgGrowthIcon', 'kpiMomAvgGrowthText', 'kpiMomAvgGrowthContainer', 'kpiMomAvgVs', currMom.avgDaily, avgDailyGrowth, momSubtitle);
    };

    // --- 4. CALENDAR HEATMAP RENDERER (Dynamic) ---
    const renderHeatmap = (targetYear, mode = 'year') => {
        const container = document.getElementById('calendarHeatmap');
        const monthContainer = document.getElementById('monthGridContainer');

        // Heatmap only uses its single year filter now, but KPI stats 
        // will rely on the global selections.
        const globalYearSelect = document.getElementById('globalYearSelect');
        const globalCompareSelect = document.getElementById('globalCompareSelect');
        const compareYear = globalCompareSelect && globalCompareSelect.value ? globalCompareSelect.value : ((globalYearSelect ? globalYearSelect.value : targetYear) - 1);
        const kpiTargetYear = globalYearSelect ? globalYearSelect.value : targetYear;

        if (!container) return;

        const allData = data.heatmapData || [];
        const yearData = allData.filter(d => d.year == targetYear);

        let maxVal = 0;
        let minVal = Infinity;
        const dataMap = {};
        yearData.forEach(d => {
            dataMap[d.date] = d.value;
            if (d.value > maxVal) maxVal = d.value;
            if (d.value < minVal) minVal = d.value;
        });
        if (minVal === Infinity) minVal = 0;

        const getColor = (val) => {
            if (val === 0) return 'bg-slate-100';
            const range = maxVal - minVal;
            const pct = range > 0 ? (val - minVal) / range : 0;
            if (pct < 0.25) return 'bg-indigo-200';
            if (pct < 0.50) return 'bg-indigo-400';
            if (pct < 0.75) return 'bg-indigo-600';
            return 'bg-[#1F3C88]';
        };

        // Update KPI Stats based on the Global Filters independently of Heatmap Target Year
        updateSeasonalStats(kpiTargetYear, compareYear, allData);

        if (mode === 'year') {
            container.innerHTML = '';
            // Make the outer container a responsive grid
            container.className = 'grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-6 gap-6 place-items-center w-full';
            // Also reset explicit inline styles applied previously if any
            container.style.display = '';
            container.style.gap = '';

            let html = '';
            const monthsNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'];

            for (let m = 0; m < 12; m++) {
                const mStart = new Date(targetYear, m, 1);
                const mEnd = new Date(targetYear, m + 1, 0);

                html += `<div class="flex flex-col gap-2 items-center w-full max-w-[200px]">`;
                html += `<span class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">${monthsNames[m]}</span>`;
                html += `<div class="grid grid-cols-7 gap-[3px] bg-white p-3 rounded-xl shadow-sm border border-slate-100 hover:shadow-md transition-shadow w-full">`;

                // Header for days of week (Senin - Minggu)
                ['S', 'S', 'R', 'K', 'J', 'S', 'M'].forEach(d => {
                    html += `<div class="text-[8px] sm:text-[9px] text-center text-slate-300 font-bold mb-1">${d}</div>`;
                });

                // Pad start of first week based on day of week (Mon=0 .. Sun=6 for Indonesian standard)
                const startDay = mStart.getDay();
                const padDays = startDay === 0 ? 6 : startDay - 1;

                for (let i = 0; i < padDays; i++) {
                    html += `<div class="w-full pt-[100%] rounded-sm bg-transparent relative"></div>`;
                }

                for (let d = 1; d <= mEnd.getDate(); d++) {
                    const y = targetYear;
                    const mo = String(m + 1).padStart(2, '0');
                    const dayStr = String(d).padStart(2, '0');
                    const dateKey = `${y}-${mo}-${dayStr}`;
                    const val = dataMap[dateKey] || 0;

                    html += `<div class="w-full pt-[100%] rounded-[3px] sm:rounded-md ${getColor(val)} transition-all hover:scale-125 hover:z-10 hover:shadow-md cursor-pointer relative group"
                                    onmouseenter="showHeatmapTooltip(event, '${dateKey}', ${val})"
                                    onmousemove="moveHeatmapTooltip(event)"
                                    onmouseleave="hideHeatmapTooltip()">
                                    <div class="absolute inset-0"></div>    
                            </div>`;
                }

                html += `</div></div>`;
            }
            container.innerHTML = html;
        }

        if (mode === 'month') {
            monthContainer.innerHTML = '';
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'];

            months.forEach((mName, mIdx) => {
                const mStart = new Date(targetYear, mIdx, 1);
                const mEnd = new Date(targetYear, mIdx + 1, 0);

                let calHTML = `<div class="bg-white p-2.5 rounded-xl border border-slate-100 w-full shadow-sm hover:shadow-md transition-shadow">
                                <h4 class="text-xs font-bold text-slate-600 mb-2 text-center uppercase tracking-wider">${mName}</h4>
                                <div class="grid grid-cols-7 gap-0.5">`;

                ['M', 'S', 'S', 'R', 'K', 'J', 'S'].forEach(d => {
                    calHTML += `<div class="text-[8px] text-center text-slate-400 font-bold">${d}</div>`;
                });

                const startD = mStart.getDay();
                for (let i = 0; i < startD; i++) calHTML += `<div></div>`;

                for (let d = 1; d <= mEnd.getDate(); d++) {
                    const dateKey = `${targetYear}-${String(mIdx + 1).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
                    const val = dataMap[dateKey] || 0;

                    calHTML += `<div class="w-5 h-5 flex items-center justify-center text-[9px] rounded-md ${getColor(val)} ${val > 0 && maxVal > 0 && (val - minVal) / (maxVal - minVal) > 0.5 ? 'text-white' : 'text-slate-600'} cursor-pointer hover:scale-110 transition-transform" 
                                    onmouseenter="showHeatmapTooltip(event, '${dateKey}', ${val})"
                                    onmousemove="moveHeatmapTooltip(event)"
                                    onmouseleave="hideHeatmapTooltip()">${d}</div>`;
                }
                calHTML += `</div></div>`;
                monthContainer.innerHTML += calHTML;
            });
        }
    };

    window.showHeatmapTooltip = (e, dateStr, val) => {
        const tooltipEl = document.getElementById('heatmapTooltip');
        const htDate = document.getElementById('htDate');
        const htValue = document.getElementById('htValue');

        if (!tooltipEl) return;

        const d = new Date(dateStr);
        const formattedDate = d.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });

        htDate.textContent = formattedDate;
        htValue.innerHTML = `<span class="font-bold text-indigo-300 text-sm">${val.toLocaleString()}</span> Penerbangan`;

        tooltipEl.classList.remove('hidden');

        const x = e.clientX;
        const y = e.clientY - 10;
        tooltipEl.style.left = `${x}px`;
        tooltipEl.style.top = `${y}px`;
        tooltipEl.style.opacity = '1';
    };

    window.moveHeatmapTooltip = (e) => {
        const tooltipEl = document.getElementById('heatmapTooltip');
        if (!tooltipEl) return;
        const x = e.clientX;
        const y = e.clientY - 10;
        tooltipEl.style.left = `${x}px`;
        tooltipEl.style.top = `${y}px`;
    };

    window.hideHeatmapTooltip = () => {
        const tooltipEl = document.getElementById('heatmapTooltip');
        if (tooltipEl) {
            tooltipEl.classList.add('hidden');
            tooltipEl.style.opacity = '0';
        }
    };

    window.toggleHeatmapView = (mode) => {
        const yBtn = document.getElementById('btnViewYear');
        const mBtn = document.getElementById('btnViewMonth');
        const yView = document.getElementById('heatmapYearView');
        const mView = document.getElementById('heatmapMonthView');
        const hmSelect = document.getElementById('heatmapSingleYearSelect');
        const currentYear = hmSelect ? hmSelect.value : new Date().getFullYear();

        const activeClasses = ['bg-white', 'text-indigo-600', 'shadow-sm'];
        const inactiveClasses = ['text-slate-500', 'hover:text-slate-700', 'hover:bg-white/50'];

        if (mode === 'year') {
            yBtn.classList.add(...activeClasses);
            yBtn.classList.remove(...inactiveClasses);

            mBtn.classList.remove(...activeClasses);
            mBtn.classList.add(...inactiveClasses);

            yView.classList.remove('hidden');
            mView.classList.add('hidden');
        } else {
            mBtn.classList.add(...activeClasses);
            mBtn.classList.remove(...inactiveClasses);

            yBtn.classList.remove(...activeClasses);
            yBtn.classList.add(...inactiveClasses);

            yView.classList.add('hidden');
            mView.classList.remove('hidden');
        }

        renderHeatmap(currentYear, mode);
    };

    // --- EVENT LISTENERS FOR FILTERS ---
    const heatmapSingleSelect = document.getElementById('heatmapSingleYearSelect');
    if (heatmapSingleSelect) {
        heatmapSingleSelect.addEventListener('change', (e) => {
            renderHeatmap(e.target.value, 'year');
        });
    }

    const globalYearSelect = document.getElementById('globalYearSelect');
    const globalCompareSelect = document.getElementById('globalCompareSelect');

    const updateGlobalKPIs = () => {
        if (globalYearSelect) {
            // Trigger renderHeatmap to recalculate stats quietly or call updateSeasonalStats directly
            // In this architecture, calling renderHeatmap with Heatmap's current year will trigger KPI updates 
            // since we modified renderHeatmap to look at the global selects.
            const hmYear = heatmapSingleSelect ? heatmapSingleSelect.value : new Date().getFullYear();
            renderHeatmap(hmYear, 'year');
        }
    };

    if (globalYearSelect) {
        globalYearSelect.addEventListener('change', updateGlobalKPIs);
    }
    if (globalCompareSelect) {
        globalCompareSelect.addEventListener('change', updateGlobalKPIs);
    }

    // Initial Render
    // Since the Heatmap toggle button isn't needed anymore, let's just make sure
    // renderHeatmap kicks off correctly based on globalYearSelect's initially selected option
    const initHmYear = heatmapSingleSelect ? heatmapSingleSelect.value : new Date().getFullYear();
    renderHeatmap(initHmYear, 'year');
});
