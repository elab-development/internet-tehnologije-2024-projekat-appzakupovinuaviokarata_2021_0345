export type BookingStatus =
  | 'PENDING' | 'CONFIRMED' | 'CANCELLED'
  | 'pending' | 'confirmed' | 'cancelled';

export interface FareLite {
  id: number; name?: string | null; price: number; class?: string | null;
}

export interface ContactInfo {
  name?: string | null;
  email?: string | null;
  phone?: string | null;
}

export interface FareLite {
  id: number;
  name?: string | null;
  class?: string | null;
  price: number;
  currency?: string | null;
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
  passengers?: number | null;
  contact?: ContactInfo | null;
  flight?: FlightLite | null;  
  fare?:   FareLite   | null;
}
