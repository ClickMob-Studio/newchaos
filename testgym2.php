<?php 
require "header.php";
?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<canvas id="statsChart" width="400" height="400"></canvas>
    <script>
        fetch('testgym.php')
            .then(response => response.json())
            .then(data => {
                const dates = data.map(item => item.record_date);
                const strengths = data.map(item => item.strength);
                const defenses = data.map(item => item.defense);
                const speeds = data.map(item => item.speed);

                const ctx = document.getElementById('statsChart').getContext('2d');
                const statsChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: dates,
                        datasets: [
                            {
                                label: 'Strength',
                                data: strengths,
                                borderColor: 'red',
                                backgroundColor: 'rgba(255, 0, 0, 0.1)',
                            },
                            {
                                label: 'Defense',
                                data: defenses,
                                borderColor: 'green',
                                backgroundColor: 'rgba(0, 255, 0, 0.1)',
                            },
                            {
                                label: 'Speed',
                                data: speeds,
                                borderColor: 'blue',
                                backgroundColor: 'rgba(0, 0, 255, 0.1)',
                            }
                        ]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            })
            .catch(error => console.error('Error loading the data: ', error));
    </script>