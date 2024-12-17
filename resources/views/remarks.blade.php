<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chart Remarks</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f8f9fa;
        }
        canvas {
            max-width: 400px;
            max-height: 400px;
        }
    </style>
</head>
<body>
    <canvas id="myChart"></canvas>

    <script>
        // Parse the chart data passed from the controller
        const chartData = {!! json_encode($chart->chart_data) !!};

        const ctx = document.getElementById('myChart').getContext('2d');
        const myChart = new Chart(ctx, {
            type: chartData.type, // Chart type from the database
            data: chartData.data, // Data for the chart
            options: chartData.options // Options for the chart
        });
    </script>
</body>
</html>