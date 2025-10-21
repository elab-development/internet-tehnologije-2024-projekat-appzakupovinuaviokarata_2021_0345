import { Component, Input } from '@angular/core';
import { CommonModule } from '@angular/common';
import { AbstractControl, ReactiveFormsModule } from '@angular/forms';

@Component({
  selector: 'app-ui-input',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule],
  template: `
    
  `,
  styleUrls: ['./ui-input.scss']
})
export class UiInput{
  @Input() label = '';
  @Input() placeholder = '';
  @Input() type: 'text'|'email'|'number'|'date' = 'text';
  @Input() control!: AbstractControl;
  @Input() inputmode?: string;
  @Input() min?: string | number;
  @Input() max?: string | number;

  get showError() {
    return this.control && this.control.touched && this.control.invalid;
  }
  get firstError(): string {
    if (!this.control?.errors) return '';
    if (this.control.errors['required']) return 'This field is required';
    if (this.control.errors['minlength']) return 'Too short';
    if (this.control.errors['maxlength']) return 'Too long';
    if (this.control.errors['email']) return 'Invalid email';
    return 'Invalid value';
  }
}
