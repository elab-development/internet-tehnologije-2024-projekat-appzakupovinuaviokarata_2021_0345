import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { UiButton } from '../../../components/shared/ui-button';
import { UiModal } from '../../../components/shared/ui-modal';

@Component({
  standalone: true,
  selector: 'app-bookings',
  imports: [CommonModule, UiModal, UiButton],
  template: `
  
  `
})
export class Bookings {
  open = false;
  confirm(){ this.open = false; }
}
