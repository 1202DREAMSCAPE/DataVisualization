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
        if (in_array($this->chartType, ['bar', 'line','polarArea']) && (!$this->xAxis || !$this->yAxis)) {
            return; // Wait until both X and Y axes are selected
        }

        if ($this->chartType === 'pie' && !$this->xAxis) {
            return; // Wait until X-Axis is selected
        }

        if ($this->chartType === 'radialBar' && !$this->selectedMetric) {
            return; // Wait until metric is selected
        }

        if (in_array($this->chartType, ['radar']) && empty($this->selectedCategories)) {
            return; // Wait until categories are selected
        }

        // Prepare the chart data based on the chart type
        switch ($this->chartType) {
            case 'bar':
                $this->prepareBarOrLineChartData("bar");
                break;
            case 'line':
                $this->prepareBarOrLineChartData("line");
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
                'labels' => $this->chartData['labels'] ?? [],
                // Removed 'labels' as it’s not used in the prepared chartData
                'id' => $this->id,
            ]);
        }
    }

    private function prepareBarOrLineChartData($type)
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
            'chartType' => $type ?? null, // Ensure chartType is set, default to 'bar'
    
            'series' => [
                [
                    'name' => $this->seriesName ?? ' ', // Dynamic series name with default
                    'data' => !empty($filteredData) ? $filteredData : [0], // Prepared or default data
                ],
            ],
    
            'xaxis' => [
                'categories' => !empty($filteredCategories) ? $filteredCategories : [''], // Prepared or default categories
                'title' => [
                    'text' => $this->xAxisLabel ?? ' ', // Dynamic x-axis label with default
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
        if (!$this->xAxis || !$this->yAxis) {
            return; // Ensure both X-Axis (labels) and Y-Axis (values) are set
        }
    
        // Helper function to convert column letters to indices
        $columnLetterToIndex = function (string $letter): int {
            $letter = strtoupper($letter);
            $length = strlen($letter);
            $index = 0;
    
            for ($i = 0; $i < $length; $i++) {
                $index = $index * 26 + (ord($letter[$i]) - ord('A') + 1);
            }
    
            return $index - 1; // Zero-based index
        };
    
        $xIndex = $columnLetterToIndex($this->xAxis);
        $yIndex = $columnLetterToIndex($this->yAxis);
    
        // Validate that the indices exist in the data rows
        foreach ($this->data as $rowNumber => $row) {
            if (!array_key_exists($xIndex, $row)) {
                dd("Missing xAxis index {$xIndex} in row {$rowNumber}", $row);
            }
            if (!array_key_exists($yIndex, $row)) {
                dd("Missing yAxis index {$yIndex} in row {$rowNumber}", $row);
            }
        }
    
        // Accumulate values based on the X-Axis labels
        $aggregatedData = [];
        foreach ($this->data as $row) {
            $label = $row[$xIndex];
            $value = is_numeric($row[$yIndex]) ? (float) $row[$yIndex] : 0;
    
            if (!isset($aggregatedData[$label])) {
                $aggregatedData[$label] = 0;
            }
            $aggregatedData[$label] += $value;
        }
    
        // Prepare the series and labels for the pie chart
        $this->chartData = [
            'chartType' => "pie", // Ensure chartType is set, default to 'bar'
            'series' => array_values($aggregatedData), // Y-Axis values
            'labels' => array_keys($aggregatedData),  // X-Axis labels
            'options' => [
                'chart' => [
                    'type' => 'pie',
                ],
                'title' => [
                    'text' => 'Pie Chart',
                    'align' => 'center',
                ],
                'responsive' => [
                    [
                        'breakpoint' => 480,
                        'options' => [
                            'chart' => [
                                'width' => 200,
                            ],
                            'legend' => [
                                'position' => 'bottom',
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
    

    private function prepareRadarChartData()
    {
        // Ensure selected categories and data are valid
        if (empty($this->selectedCategories) || empty($this->data)) {
            dd('Selected categories or data are missing');
            return;
        }
    
        /**
         * Convert a column letter (e.g., 'A', 'B', 'AA') to a zero-based index.
         *
         * @param string $letter The column letter.
         * @return int The corresponding zero-based index.
         */
        $columnLetterToIndex = function (string $letter): int {
            $letter = strtoupper($letter);
            $length = strlen($letter);
            $index = 0;
    
            for ($i = 0; $i < $length; $i++) {
                $index = $index * 26 + (ord($letter[$i]) - ord('A') + 1);
            }
    
            return $index - 1; // Zero-based index
        };
    
        // Initialize datasets and a global max value tracker
        $datasets = [];
        $globalMaxValues = [];
    
        foreach ($this->selectedCategories as $category) {
            // Convert category to numeric index (if using letters)
            $columnIndex = is_numeric($category) ? $category : $columnLetterToIndex($category);
    
            // Extract column data and ensure it is numeric
            $columnData = array_map(function ($row) use ($columnIndex) {
                return isset($row[$columnIndex]) && is_numeric($row[$columnIndex]) ? (float) $row[$columnIndex] : null;
            }, $this->data);
    
            // Filter out null values (non-numeric data)
            $columnData = array_filter($columnData, fn($value) => $value !== null);
    
            if (empty($columnData)) {
                // Skip this column if it has no valid numeric data
                continue;
            }
    
            // Get the top 6 values (descending order), padded if necessary
            rsort($columnData);
            $topValues = array_slice($columnData, 0, 6);
            while (count($topValues) < 6) {
                $topValues[] = 0; // Pad with 0
            }
    
            // Add to the dataset
            $datasets[] = [
                'name' => $this->headers[$category] ?? $category, // Use column name or fallback
                'data' => $topValues,
            ];
    
            // Merge values into global max array
            $globalMaxValues = array_merge($globalMaxValues, $topValues);
        }
    
        // Ensure there are numeric values to calculate the highest value
        if (empty($globalMaxValues)) {
            dd('No numeric data available to generate chart');
            return;
        }
    
        // Determine the highest value across all selected columns
        $highestValue = max($globalMaxValues);
    
        // Set labels as the highest value repeated 6 times
        $labels = array_fill(0, 6, (string) $highestValue);
    
        // Prepare final chart data
        $this->chartData = [
            'chartType' => "radar", // Set the chart type
            'series' => $datasets,
            'labels' => $labels,
            'options' => [
                'title' => [
                    'text' => 'Radar Chart',
                    'align' => 'center',
                    'style' => [
                        'fontSize' => '20px',
                        'fontWeight' => 'bold',
                        'color' => '#263238',
                    ],
                ],
            ],
        ];
    
        // Uncomment to debug the final chart data
        // dd($this->chartData);
    }
    
    
    
    
    private function preparePolarAreaChartData()
    {
        // Ensure x-axis and y-axis are set and valid
        if (empty($this->xAxis) || empty($this->yAxis) || empty($this->data)) {
            dd('xAxis, yAxis, or data is missing');
            return;
        }
    
        /**
         * Convert a column letter (e.g., 'A', 'B', 'AA') to a zero-based index.
         *
         * @param string $letter The column letter.
         * @return int The corresponding zero-based index.
         */
        $columnLetterToIndex = function (string $letter): int {
            $letter = strtoupper($letter);
            $length = strlen($letter);
            $index = 0;
    
            for ($i = 0; $i < $length; $i++) {
                $index = $index * 26 + (ord($letter[$i]) - ord('A') + 1);
            }
    
            return $index - 1; // Zero-based index
        };
    
        // Convert x-axis and y-axis to numeric indices
        $xIndex = is_numeric($this->xAxis) ? $this->xAxis : $columnLetterToIndex($this->xAxis);
        $yIndex = is_numeric($this->yAxis) ? $this->yAxis : $columnLetterToIndex($this->yAxis);
    
        // Aggregate y-axis values for each unique x-axis entry
        $aggregatedData = [];
        foreach ($this->data as $row) {
            if (!isset($row[$xIndex]) || !isset($row[$yIndex]) || !is_numeric($row[$yIndex])) {
                continue; // Skip invalid rows
            }
    
            $xValue = $row[$xIndex];
            $yValue = (float)$row[$yIndex];
    
            if (!isset($aggregatedData[$xValue])) {
                $aggregatedData[$xValue] = 0;
            }
    
            $aggregatedData[$xValue] += $yValue;
        }
    
        // Sort aggregated data in descending order of y-values
        arsort($aggregatedData);
    
        // Extract the top 6 entries
        $topEntries = array_slice($aggregatedData, 0, 6, true);
    
        // Prepare series and labels
        $series = array_values($topEntries); // Accumulated y-values
        $labels = array_keys($topEntries);  // Unique x-values
    
        // Prepare the final chart data
        $this->chartData = [
            'chartType' => "polarArea", // Set the chart type
            'series' => $series,
            'labels' => $labels,
            'options' => [
                'chart' => [
                    'width' => 380,
                    'type' => 'polarArea',
                ],
                'fill' => [
                    'opacity' => 1,
                ],
                'stroke' => [
                    'width' => 1,
                ],
                'yaxis' => [
                    'show' => false,
                ],
                'legend' => [
                    'position' => 'bottom',
                ],
                'plotOptions' => [
                    'polarArea' => [
                        'rings' => [
                            'strokeWidth' => 0,
                        ],
                        'spokes' => [
                            'strokeWidth' => 0,
                        ],
                    ],
                ],
                'theme' => [
                    'monochrome' => [
                        'enabled' => true,
                        'shadeTo' => 'light',
                        'shadeIntensity' => 0.6,
                    ],
                ],
                'title' => [
                    'text' => 'Polar Area Chart',
                    'align' => 'center',
                ],
            ],
        ];
    
        // Uncomment the following line for debugging
        //dd($this->chartData);
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