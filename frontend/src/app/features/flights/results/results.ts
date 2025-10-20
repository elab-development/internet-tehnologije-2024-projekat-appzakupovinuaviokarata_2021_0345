import { Component, OnInit, inject } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { ApiService } from '../../../core/api.service';
import { DatePipe } from '@angular/common';
import { NgIf, NgFor } from '@angular/common';



@Component({
  selector: 'app-results',
  standalone: true,
  imports: [NgIf, NgFor, DatePipe],
  templateUrl: './results.html',
  styleUrl: './results.scss'
})

export class Results implements OnInit {

  private route = inject(ActivatedRoute);
  private api = inject(ApiService);

  rows: any[] = [];
  loading = false;
  error = '';

  from = '';
  to = '';
  date = '';

  
  ngOnInit(): void {
    this.route.queryParams.subscribe(p => {
      const from = (p['from'] || '').toUpperCase();
      const to   = (p['to']   || '').toUpperCase();
      const date = p['date'] || '';

      if (!this.from || !this.to) {
        this.rows = [];
        this.error = 'Pick origin and destination (e.g. BEG → AMS).';
        return;
      }

      this.fetch(from, to, date);
    });
  }

  private fetch(from: string, to: string, date: string) {
    this.loading = true; this.error = '';
    this.api.get<any>('/flights/search', { from, to, date, per_page: 20 })
      .subscribe({
        next: (res) => { this.rows = res?.data ?? res; this.loading = false; },
        error: (e) => { this.error = e?.error?.message || 'Failed to load flights'; this.loading = false; },
      });
  }

}
