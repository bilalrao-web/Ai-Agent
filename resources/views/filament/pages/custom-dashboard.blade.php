<x-filament-panels::page>
    <div class="sticky-dashboard-container">
        <!-- Sticky Top Stats Row -->
        <div class="sticky-stats-row">
            <div class="stats-grid">
                @foreach($this->getCachedStats() as $stat)
                    <div class="stat-card">
                        {{ $stat }}
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Fixed Charts Section -->
        <div class="fixed-charts-section">
            <div class="charts-grid">
                @foreach($this->getCachedCharts() as $chart)
                    <div class="chart-card">
                        {{ $chart }}
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Scrollable Tables Section -->
        <div class="scrollable-tables-section">
            <div class="tables-grid">
                @foreach($this->getCachedTables() as $table)
                    <div class="table-card">
                        {{ $table }}
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-filament-panels::page>
