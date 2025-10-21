import { Component, EventEmitter, Input, Output } from '@angular/core';
import { NgIf } from '@angular/common';
import { UiButton } from './ui-button';


@Component({
  selector: 'app-ui-modal',
  standalone: true,
  imports: [NgIf, UiButton],
  template: `
  `,
  styleUrls: ['./ui-modal.scss']
})
export class UiModal{
  @Input() open = false;
  @Input() title = 'Dialog';
  @Output() close = new EventEmitter<void>();
}
