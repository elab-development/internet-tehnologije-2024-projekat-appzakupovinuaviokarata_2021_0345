import { inject, Injectable } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { Booking } from '../../core/models/booking/booking';


interface Paginated<T> {
  data: T[];
  meta?: { current_page: number; last_page: number; total: number; };
}

@Injectable({ providedIn: 'root' })
export class BookingsService {
  private http = inject(HttpClient);
  private base = 'http://127.0.0.1:8000/api/bookings';


  list(page = 1, perPage = 10): Observable<Paginated<Booking>> {
    const params = new HttpParams().set('page', page).set('per_page', perPage);
    return this.http.get<Paginated<Booking>>(this.base, { params });
  }

  get(id: number) {
    return this.http.get<Booking>(`${this.base}/${id}`);
  }

  create(payload: { flight_id: number; fare_id: number; passengers?: number; contact?: any }) {
    return this.http.post<Booking>(this.base, payload);
  }
  update(id: number, payload: Partial<{ passengers: number; contact: any }>) {
    return this.http.put<Booking>(`${this.base}/${id}`, payload);
  }
  cancel(id: number) {
    return this.http.delete<{message:string}>(`${this.base}/${id}`);
  }
}
