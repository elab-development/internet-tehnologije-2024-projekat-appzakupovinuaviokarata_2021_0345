import { Component } from '@angular/core';
import { DatePipe } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { Router } from '@angular/router';

@Component({
  selector: 'app-search',
  imports: [  FormsModule, DatePipe],
  templateUrl: './search.html',
  styleUrl: './search.scss'
})
export class Search {
  from = 'BEG';
  to = 'AMS';
  date = '';

  constructor(private router: Router) {}

  go() {
    this.router.navigate(['/flights/results'], {
      queryParams: { from: this.from, to: this.to, date: this.date || undefined },
    });
  }

}
