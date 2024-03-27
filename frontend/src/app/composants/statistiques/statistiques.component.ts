import { Component, OnInit, AfterViewInit, ViewChild, ElementRef } from '@angular/core';
import { Chart, ChartConfiguration, ChartData, ChartOptions, LinearScale, CategoryScale, BarController, registerables  } from 'chart.js';
import { AppModule } from 'src/app/app.module';

@Component({
  selector: 'app-statistiques',
  templateUrl: './statistiques.component.html',
  styleUrls: ['./statistiques.component.scss']
})
export class StatistiquesComponent implements OnInit, AfterViewInit {
  @ViewChild('chartLikes', { static: true }) chartLikes!: ElementRef<HTMLCanvasElement>;
  @ViewChild('chartAges', { static: true }) chartAges!: ElementRef<HTMLCanvasElement>;

  chart!: Chart;
  
  statistiques: any = null;

  constructor(private appModule: AppModule) { }

  ngOnInit() {
    Chart.register(...registerables);
    
    const url = 'api/statistiques';
    
    this.appModule.request(url, 'GET').subscribe((data_brute: any) =>
    {
      this.statistiques = data_brute;

      let apiResponse = data_brute.age

      const counts = apiResponse.map((item: { count: any; }) => item.count);
      const years = apiResponse.map((item: { age: any; }) => item.age);

      console.log('data', data_brute);
      // Retrieve or generate your data for the chart
      const data: ChartData = {
        labels: years,
        datasets: [
          {
            label: 'Age',
            data: counts,
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            borderColor: 'rgba(75, 192, 192, 1)',
            borderWidth: 1,
          }
        ]
      };

      // Create the chart configuration
      const chartConfig: ChartConfiguration<'bar'> = {
        type: 'bar',
        data: data as any, // Workaround for TypeScript error: Type 'ChartData' is missing the following properties from type 'ChartData': datasets, labels
        options: {
          height: 100,
          responsive: true,
          scales: {
            y: {
              type: 'linear',
              beginAtZero: true
            }
          }
        } as ChartOptions<'bar'> // Use type assertion for options
      };

      // Create the chart using Chart.js
      if(!this.chartAges) return console.log('chartAges is null');
      const ctx = this.chartAges.nativeElement.getContext('2d')!;
      this.chart = new Chart(ctx, chartConfig);
      // console.log('chart', this.chart);
    });

  }

  ngAfterViewInit() {
    // This method will be called after the view has been initialized
    // and the native elements are accessible.
    // You can perform additional chart-related operations here if needed.
  }
}