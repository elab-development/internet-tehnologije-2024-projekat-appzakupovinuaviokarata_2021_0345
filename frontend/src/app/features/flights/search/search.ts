import { Component, signal, inject } from '@angular/core';
import { CommonModule, DatePipe, CurrencyPipe } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { SearchService, FlightHit } from './search.service';

@Component({
  standalone: true,
  selector: 'app-home-search',
  imports: [CommonModule, FormsModule],
  templateUrl: './search.html',
  styleUrls: ['./search.scss'],
})
export class Search{
  private api = inject(SearchService);

  from = 'BEG';
  to = 'AMS';
  depart?: string;
  ret?: string;
  pax = 1;
  cabin = 'economy';
  direct = false;

  loading = signal(false);
  error   = signal<string | null>(null);
  results = signal<FlightHit[]>([]);

  onSearch() {
    this.loading.set(true);
    this.error.set(null);
    this.api.search({ from: this.from, to: this.to, depart: this.depart, ret: this.ret, pax: this.pax, cabin: this.cabin, direct: this.direct })
      .subscribe({
        next: (hits) => { this.results.set(hits); this.loading.set(false); },
        error: () => { this.error.set('Could not load flights.'); this.loading.set(false); }
      });
  }

  cheapestFare(f?: FlightHit) {
    const fares = f?.fares ?? [];
    if (!fares.length) return null;
    return fares.slice().sort((a,b)=> (a.price??Infinity) - (b.price??Infinity))[0];
  }

  fmt(d?: string|null) { return d ? new Date(d) : null; }
}
