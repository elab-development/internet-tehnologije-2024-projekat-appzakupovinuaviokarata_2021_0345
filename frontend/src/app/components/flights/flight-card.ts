import { Component, EventEmitter, Input, Output } from '@angular/core';
import { CommonModule } from '@angular/common';
import { UiButton } from '../shared/ui-button';


export interface FlightVM {
  id: number;
  flight_no: string;
  from: string; to: string;
  dep_time: string; arr_time: string;
  carrier_code?: string;
  price_eur?: number;
  stops?: number;
}

@Component({
  selector: 'app-flight-card',
  standalone: true,
  imports: [CommonModule, UiButton],
  template: `
  `,
  styleUrls: ['./flight-card.scss']
})
export class FlightCard {
  @Input({ required: true }) vm!: FlightVM;
  @Output() select = new EventEmitter<FlightVM>();
}
