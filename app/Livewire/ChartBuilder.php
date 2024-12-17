<?php

namespace App\Livewire;

use Livewire\Component;

class ChartBuilder extends Component
{
    public $headers = [];
    public $data = [];
    public $chartType = '';
    public $xAxis = null;
    public $yAxis = null;
    public $selectedCategories = [];
    public $selectedMetric = null;
    public $chartData = [];
    public $id; // Unique ID for charts

    // Flag to prevent multiple dispatches
    protected $hasDispatched = false;

    public function mount($headers = [], $data = [], $chartType = null)
    {
        $this->headers = $headers;
        $this->data = $data;
        $this->chartType = $chartType ?: '';
        $this->id = uniqid(); // Generate a unique ID for the chart
        $this->prepareChartData();
    }

    public function selectChartType($type)
    {
        $this->chartType = $type;
        $this->prepareChartData();
    }

    public function updated($property)
    {
        // Reset the dispatch flag on any update to allow dispatching for new valid data
        $this->hasDispatched = false;

        if ($this->chartType && (
            ($this->chartType === 'pie' && $this->xAxis) ||
            (in_array($this->chartType, ['bar', 'line']) && $this->xAxis && $this->yAxis) ||
            ($this->chartType === 'radialBar' && $this->selectedMetric) ||
            (in_array($this->chartType, ['radar', 'polarArea']) && !empty($this->selectedCategories))
        )) {
            $this->prepareChartData();
        }
    }

    private function prepareChartData()
    {
        if (!$this->chartType) return;

        // Validate required fields based on chart type
        if (in_array($this->chartType, ['bar', 'line']) && (!$this->xAxis || !$this->yAxis)) {
            return; // Wait until both X and Y axes are selected
        }

        if ($this->chartType === 'pie' && !$this->xAxis) {
            return; // Wait until X-Axis is selected
        }

        if ($this->chartType === 'radialBar' && !$this->selectedMetric) {
            return; // Wait until metric is selected
        }

        if (in_array($this->chartType, ['radar', 'polarArea']) && empty($this->selectedCategories)) {
            return; // Wait until categories are selected
        }

        // Prepare the chart data based on the chart type
        switch ($this->chartType) {
            case 'bar':
            case 'line':
                $this->prepareBarOrLineChartData();
                break;
            case 'pie':
                $this->preparePieChartData();
                break;
            case 'radar':
                $this->prepareRadarChartData();
                break;
            case 'polarArea':
                $this->preparePolarAreaChartData();
                break;
            case 'radialBar':
                $this->prepareRadialBarChartData();
                break;
        }

        // Dispatch the event only if chart data is valid and not yet dispatched
        if (!empty($this->chartData['series']) && !$this->hasDispatched) {
            // Set the flag to prevent further dispatches in this update cycle
            $this->hasDispatched = true;
            $this->dispatch('update-chart', [
                'chartType' => $this->chartData['chartType'] ?? 'bar', // Pass chartType directly
                'series' => $this->chartData['series'],
                'xaxis' => $this->chartData['xaxis'] ?? [],
                'yaxis' => $this->chartData['yaxis'] ?? [],
                'options' => $this->chartData['options'] ?? [],
                // Removed 'labels' as it’s not used in the prepared chartData
                'id' => $this->id,
            ]);
        }
    }

    private function prepareBarOrLineChartData()
    {
        // Ensure xAxis and yAxis are set and valid
        if (!isset($this->xAxis) || !isset($this->yAxis)) {
            // Consider logging instead of using dd() in production
            dd('xAxis or yAxis not set');
            return;
        }
    
        // Ensure data structure is correct
        if (empty($this->data) || !is_array($this->data)) {
            // Consider logging instead of using dd() in production
            dd('Data is empty or not an array', $this->data);
            return;
        }
    
        /**
         * Convert a column letter (e.g., 'A', 'B', 'AA') to a zero-based index.
         *
         * @param string $letter The column letter.
         * @return int The corresponding zero-based index.
         */
        $columnLetterToIndex = function(string $letter): int {
            $letter = strtoupper($letter);
            $length = strlen($letter);
            $index = 0;
    
            for ($i = 0; $i < $length; $i++) {
                $index = $index * 26 + (ord($letter[$i]) - ord('A') + 1);
            }
    
            return $index - 1; // Zero-based index
        };
    
        // Convert xAxis and yAxis from letters to numeric indices
        $xIndex = $columnLetterToIndex($this->xAxis);
        $yIndex = $columnLetterToIndex($this->yAxis);
    
        // Validate that the converted indices exist in the data rows
        foreach ($this->data as $rowNumber => $row) {
            if (!array_key_exists($xIndex, $row)) {
                // Consider logging instead of using dd() in production
                dd("Missing xAxis index {$xIndex} in row {$rowNumber}", $row);
            }
            if (!array_key_exists($yIndex, $row)) {
                // Consider logging instead of using dd() in production
                dd("Missing yAxis index {$yIndex} in row {$rowNumber}", $row);
            }
        }
    
        // Extract x and y values
        $xValues = array_map(function($row) use ($xIndex) {
            return $row[$xIndex];
        }, $this->data);
    
        $yValues = array_map(function($row) use ($yIndex) {
            return is_numeric($row[$yIndex]) ? (float)$row[$yIndex] : 0;
        }, $this->data);
    
        // Optional: Filter out rows where 'y' is zero or 'x' is null
        $filteredCategories = [];
        $filteredData = [];
        for ($i = 0; $i < count($xValues); $i++) {
            if ($xValues[$i] !== null && $yValues[$i] !== 0) {
                $filteredCategories[] = $xValues[$i];
                $filteredData[] = $yValues[$i];
            }
        }
    
        // Reindex the arrays to ensure sequential numeric keys
        $filteredCategories = array_values($filteredCategories);
        $filteredData = array_values($filteredData);
    
        // Limit to 10 data points if necessary
        $filteredCategories = array_slice($filteredCategories, 0, 10);
        $filteredData = array_slice($filteredData, 0, 10);
    
        // Prepare chart data matching the desired structure
        $this->chartData = [
            'chartType' => $this->chartType ?? 'bar', // Ensure chartType is set, default to 'bar'
    
            'series' => [
                [
                    'name' => $this->seriesName ?? 'Sales', // Dynamic series name with default
                    'data' => !empty($filteredData) ? $filteredData : [10, 20, 30, 40, 50, 60, 70], // Prepared or default data
                ],
            ],
    
            'xaxis' => [
                'categories' => !empty($filteredCategories) ? $filteredCategories : ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'], // Prepared or default categories
                'title' => [
                    'text' => $this->xAxisLabel ?? 'Days', // Dynamic x-axis label with default
                ],
            ],
    
            'yaxis' => [
                'title' => [
                    'text' => $this->yAxisLabel ?? 'Values', // Dynamic y-axis label with default
                ],
            ],
    
            'options' => [
                'title' => [
                    'text' => ucfirst($this->chartType) . ' Chart', // Dynamic chart title based on chartType
                    'align' => 'center',
                    'style' => [
                        'fontSize' => '20px',
                        'fontWeight' => 'bold',
                        'color' => '#263238'
                    ],
                ],
            ],
        ];
    
        // Debug to verify the final chart data structure
        // Uncomment the line below to enable debugging
        //dd($this->chartData);
    }
    
    
    private function preparePieChartData()
    {
        if (!$this->xAxis) return;

        // Assuming xAxis is a column letter, convert it to index
        $columnLetterToIndex = function(string $letter): int {
            $letter = strtoupper($letter);
            $length = strlen($letter);
            $index = 0;

            for ($i = 0; $i < $length; $i++) {
                $index = $index * 26 + (ord($letter[$i]) - ord('A') + 1);
            }

            return $index - 1; // Zero-based index
        };

        $xIndex = $columnLetterToIndex($this->xAxis);

        // Validate that the xIndex exists in the data rows
        foreach ($this->data as $rowNumber => $row) {
            if (!array_key_exists($xIndex, $row)) {
                // Consider logging instead of using dd() in production
                dd("Missing xAxis index {$xIndex} in row {$rowNumber}", $row);
            }
        }

        $values = array_count_values(array_column($this->data, $xIndex));

        $this->chartData = [
            'series' => array_values($values),
            'labels' => array_keys($values),
            'options' => [
                'title' => [
                    'text' => 'Pie Chart',
                    'align' => 'center',
                ],
            ],
        ];
    }

    private function prepareRadarChartData()
    {
        if (empty($this->selectedCategories)) return;

        $datasets = array_map(function ($category) {
            return [
                'name' => $this->headers[$category] ?? 'Category',
                'data' => array_column($this->data, $category),
            ];
        }, $this->selectedCategories);

        $this->chartData = [
            'series' => $datasets,
            'labels' => array_map(function($category) {
                return $this->headers[$category] ?? 'Category';
            }, $this->selectedCategories),
            'options' => [
                'title' => [
                    'text' => 'Radar Chart',
                    'align' => 'center',
                ],
            ],
        ];
    }

    private function preparePolarAreaChartData()
    {
        if (empty($this->selectedCategories)) return;

        $values = array_map(function ($category) {
            return array_sum(array_column($this->data, $category));
        }, $this->selectedCategories);

        $labels = array_map(function ($category) {
            return $this->headers[$category] ?? 'Category';
        }, $this->selectedCategories);

        $this->chartData = [
            'series' => $values,
            'labels' => $labels,
            'options' => [
                'title' => [
                    'text' => 'Polar Area Chart',
                    'align' => 'center',
                ],
            ],
        ];
    }

    private function prepareRadialBarChartData()
    {
        if (!$this->selectedMetric) return;

        $metricIndex = array_search($this->selectedMetric, array_keys($this->headers));

        if ($metricIndex === false) {
            // Consider logging instead of using dd() in production
            dd('Selected metric not found in headers');
            return;
        }

        $values = array_column($this->data, $metricIndex);
        $validValues = array_filter($values, 'is_numeric');

        if (empty($validValues)) {
            // Consider logging instead of using dd() in production
            dd('No valid numeric data for selected metric', $values);
            return;
        }

        $average = array_sum($validValues) / count($validValues);

        $this->chartData = [
            'series' => [$average],
            'labels' => [$this->headers[$this->selectedMetric] ?? 'Metric'],
            'options' => [
                'title' => [
                    'text' => 'Gauge Chart',
                    'align' => 'center',
                ],
            ],
        ];
    }

    public function render()
    {
        return view('livewire.chart-builder');
    }
}
