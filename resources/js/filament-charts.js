// Premium LEGO Dashboard Chart Enhancements - Neon Gradients & Smooth Curves
(function() {
    'use strict';
    
    function enhanceCharts() {
        if (typeof Chart === 'undefined') {
            return;
        }
        
        const charts = Chart.instances || [];
        
        charts.forEach(chart => {
            if (!chart.canvas || !chart.config) return;
            
            const canvas = chart.canvas;
            const ctx = canvas.getContext('2d');
            
            if (chart.config.type === 'line') {
                // Apply CanvasGradient to line charts
                chart.data.datasets.forEach((dataset, index) => {
                    if (dataset.fill && typeof dataset.backgroundColor === 'string') {
                        const gradient = ctx.createLinearGradient(0, 0, 0, canvas.height);
                        
                        // Determine gradient based on border color
                        if (dataset.borderColor === '#00d4ff' || dataset.borderColor === 'rgb(0, 212, 255)') {
                            gradient.addColorStop(0, 'rgba(0, 212, 255, 0.2)');
                            gradient.addColorStop(1, 'rgba(0, 212, 255, 0)');
                        } else if (dataset.borderColor === '#7b61ff' || dataset.borderColor === 'rgb(123, 97, 255)') {
                            gradient.addColorStop(0, 'rgba(123, 97, 255, 0.2)');
                            gradient.addColorStop(1, 'rgba(123, 97, 255, 0)');
                        } else if (dataset.borderColor === '#ff6384' || dataset.borderColor === 'rgb(255, 99, 132)') {
                            gradient.addColorStop(0, 'rgba(255, 99, 132, 0.2)');
                            gradient.addColorStop(1, 'rgba(255, 99, 132, 0)');
                        } else {
                            // Default cyan gradient
                            gradient.addColorStop(0, 'rgba(0, 212, 255, 0.2)');
                            gradient.addColorStop(1, 'rgba(0, 212, 255, 0)');
                        }
                        
                        dataset.backgroundColor = gradient;
                    }
                });
                
                chart.update('none');
            }
        });
    }
    
    // Run on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(enhanceCharts, 1500);
        });
    } else {
        setTimeout(enhanceCharts, 1500);
    }
    
    // Also run after Livewire updates (for Filament)
    if (typeof Livewire !== 'undefined') {
        Livewire.hook('morph.updated', () => {
            setTimeout(enhanceCharts, 800);
        });
    }
    
    // Watch for new charts being created
    const observer = new MutationObserver(() => {
        setTimeout(enhanceCharts, 500);
    });
    
    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
})();
