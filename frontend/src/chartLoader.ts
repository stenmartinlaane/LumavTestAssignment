import { Chart } from "chart.js/auto";
import { stateManager } from './StateManager';

let chart: Chart | null = null;

export default function renderChart(state: { categories: Category[] }) {
    const ctx = document.getElementById('myChart') as HTMLCanvasElement;

    console.log(state);

    if (ctx) {
        if (chart) {
            chart.data.labels = state.categories.map(category => category.name);
            chart.data.datasets[0].data = state.categories.map(category => calcualteProductCount(category)); 
            chart.update(); 
        } else {
            chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: state.categories.map(category => category.name), 
                    datasets: [{
                        label: '# of Products',
                        data: state.categories.map(category => calcualteProductCount(category)), 
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.2)',
                            'rgba(54, 162, 235, 0.2)',
                            'rgba(255, 206, 86, 0.2)',
                            'rgba(75, 192, 192, 0.2)',
                            'rgba(153, 102, 255, 0.2)',
                            'rgba(255, 159, 64, 0.2)'
                        ],
                        borderColor: [
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(153, 102, 255, 1)',
                            'rgba(255, 159, 64, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }
    }
}

function calcualteProductCount(category: Category): number {
    let count = category.products.length;
    
    for (const subcategory of category.subCategories) {
        count += calcualteProductCount(subcategory);
    }

    return count;
}
