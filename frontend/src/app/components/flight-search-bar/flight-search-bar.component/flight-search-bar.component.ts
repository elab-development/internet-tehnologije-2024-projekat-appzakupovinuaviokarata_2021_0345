import { Component, EventEmitter, Output, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ReactiveFormsModule, FormBuilder, Validators } from '@angular/forms';
import { Router } from '@angular/router';

type TripType = 'return' | 'oneway';
type Cabin = 'ECONOMY' | 'PREMIUM_ECONOMY' | 'BUSINESS' | 'FIRST';

@Component({
  selector: 'app-flight-search-bar',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule],
  templateUrl: './flight-search-bar.component.html',
  styleUrl: './flight-search-bar.component.scss',
})
export class FlightSearchBarComponent {
  private fb = inject(FormBuilder);
  private router = inject(Router);

  @Output() submitted = new EventEmitter<any>();

  tripTypes: { key: TripType; label: string }[] = [
    { key: 'return', label: 'Return' },
    { key: 'oneway', label: 'One-way' },
  ];

  cabins: { key: Cabin; label: string }[] = [
    { key: 'ECONOMY',          label: 'Economy' },
    { key: 'PREMIUM_ECONOMY',  label: 'Premium Economy' },
    { key: 'BUSINESS',         label: 'Business' },
    { key: 'FIRST',            label: 'First' },
  ];

  form = this.fb.group({
    tripType: this.fb.control<TripType>('return', { nonNullable: true }),
    from:     this.fb.control('', { validators: [Validators.required, Validators.minLength(3), Validators.maxLength(3)] }),
    to:       this.fb.control('', { validators: [Validators.required, Validators.minLength(3), Validators.maxLength(3)] }),
    depart:   this.fb.control<string>('', []),     // yyyy-MM-dd
    ret:      this.fb.control<string>('', []),     // yyyy-MM-dd
    pax:      this.fb.control<number>(1, { nonNullable: true }),
    cabin:    this.fb.control<Cabin>('ECONOMY', { nonNullable: true }),
    nearbyFrom: this.fb.control(false),
    nearbyTo:   this.fb.control(false),
    direct:     this.fb.control(false),
  });

  swap() {
    const from = this.form.value.from?.toUpperCase() || '';
    const to   = this.form.value.to?.toUpperCase() || '';
    this.form.patchValue({ from: to, to: from });
  }

  setTripType(t: TripType) {
    this.form.patchValue({ tripType: t });
    if (t === 'oneway') this.form.patchValue({ ret: '' });
  }

  submit() {
    if (this.form.invalid) {
      this.form.markAllAsTouched();
      return;
    }
    const v = this.form.getRawValue();
    const q: any = {
      from: (v.from || '').toUpperCase(),
      to:   (v.to   || '').toUpperCase(),
      date: v.depart || undefined,
      cabin: v.cabin,
      stops: v.direct ? 0 : undefined,  
      // pax is ignored by current API but we keep it in URL if you want it later
      pax: v.pax > 1 ? v.pax : undefined,
    };
    // If return, you could also navigate with ret date for future extension
    // q.return = v.tripType === 'return' ? v.ret || undefined : undefined;

    
    this.submitted.emit(q);

    // Navigate to results
    this.router.navigate(['/flights/results'], { queryParams: q });
  }
}
