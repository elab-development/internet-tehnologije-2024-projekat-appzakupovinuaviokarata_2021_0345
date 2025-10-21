import { Component, inject, signal, effect } from '@angular/core';
import { CommonModule } from '@angular/common';
import { BookingsService } from '../bookings.service';
import { UiButton } from '../../../components/shared/ui-button';
import { UiModal } from '../../../components/shared/ui-modal';
import { Booking } from '../../../core/models/booking/booking';
import { NgIf } from '@angular/common';

@Component({
  standalone: true,
  selector: 'app-bookings',
  imports: [CommonModule, UiModal, UiButton, NgIf],
  templateUrl: './list.html',
  styleUrls: ['./list.scss'], 
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


  trackById = (_: number, x: Booking) => x.id;
}
