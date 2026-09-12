import { Chart } from 'chart.js/auto';

document.addEventListener('DOMContentLoaded', () => {
    const revenueCanvas = document.getElementById('revenue-chart');
    if (revenueCanvas) {
        const months = JSON.parse(revenueCanvas.dataset.revenueMonths);
        new Chart(revenueCanvas, {
            type: 'line',
            data: {
                labels: months.map((m) => m.label),
                datasets: [{
                    data: months.map((m) => m.total),
                    borderColor: '#A9663A',
                    backgroundColor: 'rgba(169, 102, 58, 0.08)',
                    fill: true,
                    tension: 0.3,
                    pointRadius: 3,
                    pointHoverRadius: 5.5,
                }],
            },
            options: {
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true }, x: { grid: { color: '#E9E3DA' } } },
            },
        });
    }

    const communeCanvas = document.getElementById('commune-chart');
    if (communeCanvas) {
        const rows = JSON.parse(communeCanvas.dataset.communeTotals);
        new Chart(communeCanvas, {
            type: 'bar',
            data: {
                labels: rows.map((r) => r.commune),
                datasets: [{
                    data: rows.map((r) => r.total),
                    backgroundColor: '#101E28',
                }],
            },
            options: { plugins: { legend: { display: false } } },
        });
    }
});
