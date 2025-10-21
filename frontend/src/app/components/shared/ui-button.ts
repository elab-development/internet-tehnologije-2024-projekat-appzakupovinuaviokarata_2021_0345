import { Component, Input, Output, EventEmitter } from '@angular/core';
//import { NgIf, NgClass } from '@angular/common';

@Component({
  selector: 'app-ui-button',
  standalone: true,
  imports: [],
  template: `
    
  `,
  styleUrls: ['./ui-button.scss']
})
export class UiButton {

 //same thing as uibtn


  @Input() variant: 'primary'|'secondary'|'ghost' = 'primary';
  @Input() size: 'sm'|'md'|'lg' = 'md';
  @Input() block = false;
  @Input() disabled = false;
  @Input() loading = false;

  @Output() clicked = new EventEmitter<void>();
}
