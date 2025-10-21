import { Component, EventEmitter, Input, Output } from '@angular/core';


@Component({
  selector: 'app-ui-modal',
  standalone: true,
  imports: [],
  template: `
  `,
  styleUrls: ['./ui-modal.scss']
})
export class UiModal{
  @Input() open = false;
  @Input() title = 'Dialog';
  @Output() close = new EventEmitter<void>();
}
