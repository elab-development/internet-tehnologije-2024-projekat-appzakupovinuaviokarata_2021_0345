import { Component, OnInit, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ActivatedRoute } from '@angular/router';
import { HttpClientModule, HttpClient } from '@angular/common/http';
import { FlightVM } from '../../../components/flights/flight-card';

@Component({
  standalone: true,
  selector: 'app-results',
  imports: [CommonModule, HttpClientModule],
  template: `
  
  `
})
export class Results implements OnInit {
  private route = inject(ActivatedRoute);
  private http = inject(HttpClient);

  rows: FlightVM[] = [];

  ngOnInit(): void {
    this.route.queryParams.subscribe(p => {
      const params = { from: p['from'], to: p['to'], date: p['date'] };
      this.http.get<any>('/api/flights/search', { params }).subscribe(res => {
        const data = res?.data ?? res;
        this.rows = (data || []).map((x: any) => ({
          id: x.id, flight_no: x.flight_no,
          from: x.origin?.iata, to: x.destination?.iata,
          dep_time: x.dep_time, arr_time: x.arr_time,
          carrier_code: x.carrier?.code,
          price_eur: x.fares?.[0]?.price,
          stops: x.stops
        }));
      });
    });
  }

  book(f: FlightVM) {
    
    alert(`Booking for ${f.flight_no}`);
  }
}
