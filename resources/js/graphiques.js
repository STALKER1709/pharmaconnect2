import Chart from 'chart.js/auto';

/**
 * Graphiques Chart.js des dashboards pharmacie et admin
 * (palette des maquettes Stitch : vert #00873a, bleus surface #d8e3fb).
 */
Chart.defaults.font.family = 'Inter, sans-serif';
Chart.defaults.color = '#6e7b6c';
window.PharmaConnect = window.PharmaConnect || {};

PharmaConnect.graphiqueCA = (el, labels, data) => {
    if (! el) return;

    new Chart(el, {
        type: 'line',
        data: {
            labels,
            datasets: [{
                label: 'Chiffre d\'affaires (FCFA)',
                data,
                borderColor: '#00873a',
                backgroundColor: 'rgba(0, 135, 58, 0.14)',
                fill: true,
                tension: 0.35,
                borderWidth: 3,
                pointRadius: 4,
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#006b2c',
                pointBorderWidth: 2,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { ticks: { callback: (v) => new Intl.NumberFormat('fr-FR').format(v) } },
            },
        },
    });
};

PharmaConnect.graphiqueStatuts = (el, labels, data) => {
    if (! el) return;

    new Chart(el, {
        type: 'doughnut',
        data: {
            labels,
            datasets: [{
                data,
                backgroundColor: ['#ffd9de', '#d8e3fb', '#62df7d', '#00873a', '#006b2c', '#bdcaba', '#ba1a1a'],
                borderWidth: 0,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom' } },
        },
    });
};

PharmaConnect.graphiqueBarres = (el, labels, data) => {
    if (! el) return;

    new Chart(el, {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                label: 'Quantité vendue',
                data,
                backgroundColor: '#00873a',
                borderRadius: 8,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
        },
    });
};
