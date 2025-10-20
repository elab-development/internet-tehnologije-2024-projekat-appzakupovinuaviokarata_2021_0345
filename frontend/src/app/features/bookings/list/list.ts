import { Component } from '@angular/core';
import { NgIf, NgFor, DatePipe } from '@angular/common';
import { OnInit, inject } from '@angular/core';
import { ApiService } from '../../../core/api.service';

@Component({
  selector: 'app-list',
  imports: [NgIf, NgFor, DatePipe],
  templateUrl: './list.html',
  styleUrl: './list.scss'
})
export class List {
  
  private api = inject(ApiService);
  rows:any[] = [];
  err = '';

  ngOnInit() {
    this.api.get<any>('/bookings').subscribe({
      next: r => this.rows = r?.data ?? r,
      error: e => this.err = e?.error?.message || 'Unauthorized? Login first.'
    });
  }

}
