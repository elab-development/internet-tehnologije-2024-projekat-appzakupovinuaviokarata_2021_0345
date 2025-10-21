export type BookingStatus = 'PENDING' | 'CONFIRMED' | 'CANCELLED';

export interface FareLite {
  id: number; name?: string | null; price: number; class?: string | null;
}

export interface FlightLite {
  id: number;
  code?: string | null;
  departure?: string | null;
  arrival?: string | null;
  origin?: string | null;
  destination?: string | null;
}

export interface Booking {
  id: number;
  status: BookingStatus;
  total_price: number;
  currency: string;
  created_at: string;
  fare: FareLite;
  flight: FlightLite;
}
