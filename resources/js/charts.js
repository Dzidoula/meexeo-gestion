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
                    borderColor: '#4338CA',
                    backgroundColor: 'rgba(67, 56, 202, 0.08)',
                    fill: true,
                    tension: 0.3,
                    pointRadius: 3,
                    pointHoverRadius: 5.5,
                }],
            },
            options: {
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true }, x: { grid: { color: '#E7E8F0' } } },
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
                    backgroundColor: '#6C7093',
                }],
            },
            options: { plugins: { legend: { display: false } } },
        });
    }
});
