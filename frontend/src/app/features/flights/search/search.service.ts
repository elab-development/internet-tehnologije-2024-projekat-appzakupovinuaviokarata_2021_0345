import { inject, Injectable } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable, map } from 'rxjs';

export type FlightHit = {
  id: number;
  code?: string;
  carrier?: { name?: string };
  origin?: { code?: string };
  destination?: { code?: string };
  departure_time?: string | null;
  arrival_time?: string | null;
  fares?: Array<{ id:number; name?:string; class?:string; price?:number; currency?:string }>;
};

export type SearchResponse = { data: FlightHit[] };

@Injectable({ providedIn: 'root' })
export class SearchService {
  private http = inject(HttpClient);
  private base = 'http://127.0.0.1:8000/api';

  search(params: {
    from?: string; to?: string;
    depart?: string; ret?: string;
    pax?: number; cabin?: string;
    direct?: boolean;
  }): Observable<FlightHit[]> {
    let p = new HttpParams();
    Object.entries(params).forEach(([k, v]) => {
      if (v !== undefined && v !== null && v !== '') p = p.set(k, String(v));
    });

    return this.http.get<SearchResponse>(`${this.base}/flights/search`, { params: p })
      .pipe(map(res => res.data ?? []));
  }
}
