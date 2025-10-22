import { Component, inject, signal, effect } from '@angular/core';
import { CommonModule } from '@angular/common';
import { BookingsService } from '../bookings.service';
import { UiButton } from '../../../components/shared/ui-button';
import { UiModal } from '../../../components/shared/ui-modal';
import { Booking } from '../../../core/models/booking/booking';
import { NgIf } from '@angular/common';
import { ViewEncapsulation } from '@angular/core';

@Component({
  standalone: true,
  selector: 'app-bookings',
  imports: [CommonModule, UiModal, UiButton, NgIf],
  templateUrl: './list.html',
  styleUrls: ['./list.scss'], 
  encapsulation: ViewEncapsulation.None,
})
export class Bookings {
  private api = inject(BookingsService);

  loading = signal<boolean>(true);
  error = signal<string | null>(null);

  items = signal<Booking[]>([]);
  page = signal(1);
  lastPage = signal(1);

  isModalOpen = signal(false);
  selected = signal<Booking | null>(null);

  ngOnInit() { this.fetch(); }

  fetch() {
    this.loading.set(true);
    this.error.set(null);
    this.api.list(this.page()).subscribe({
      next: (res) => {
        this.items.set(res.data);
        this.lastPage.set(res.meta?.last_page ?? 1);
        this.loading.set(false);
      },
      error: (e) => {
        this.error.set('Ne mogu da učitam rezervacije.');
        this.loading.set(false);
      }
    });
  }

  openDetails(b: Booking) {
    this.selected.set(b);
    this.isModalOpen.set(true);
  }

  closeModal() { this.isModalOpen.set(false); }

  cancelSelected() {
    const b = this.selected();
    if (!b) return;
    this.api.cancel(b.id).subscribe({
      next: () => {
        // optimistički update
        this.items.set(this.items().map(x => x.id === b.id ? { ...x, status: 'CANCELLED' } : x));
        this.isModalOpen.set(false);
      }
    });
  }

  cancel(id: number) {
    this.api.cancel(id).subscribe({
      next: () => {
        this.items.set(this.items().map(x =>
          x.id === id ? { ...x, status: 'CANCELLED' as any } : x
        ));
      }
    });
  }

  fmt(d?: string | null) {
    return d ? new Date(d) : null;
  }



  private hash(n: number, salt = 0) { return Math.abs(Math.sin(n*9301 + salt*49297) * 10000); }

  seat(id: number, which: 'out'|'ret') {
    const n = Math.floor(this.hash(id, which === 'out' ? 1 : 2) % 28) + 1;
    const letters = ['A','B','C','D','E','F'];
    return `${n}${letters[Math.floor(this.hash(id, which === 'out' ? 3 : 4) % letters.length)]}`;
  }

  bagsKg(id: number) {
    return [10,15,20][Math.floor(this.hash(id, 5) % 3)];
  }

  fast(id: number) {
    return this.hash(id, 6) % 2 < 1; // true/false
  }

  classColor(v?: string | null) {
    const k = (v || 'economy').toLowerCase();
    if (k.includes('first'))    return 'is-first';
    if (k.includes('business')) return 'is-business';
    return 'is-economy';
  }


  trackById = (_: number, x: Booking) => x.id;
}
