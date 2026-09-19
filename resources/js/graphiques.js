/**
 * Graphiques Chart.js des dashboards pharmacie et admin.
 */
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
                borderColor: '#16a34a',
                backgroundColor: 'rgba(22, 163, 74, 0.12)',
                fill: true,
                tension: 0.35,
                borderWidth: 2,
                pointRadius: 3,
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
                backgroundColor: ['#f59e0b', '#0ea5e9', '#6366f1', '#06b6d4', '#22c55e', '#94a3b8', '#ef4444'],
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
                backgroundColor: '#22c76a',
                borderRadius: 6,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
        },
    });
};
