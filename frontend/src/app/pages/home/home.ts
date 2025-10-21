import { Component, Input } from '@angular/core';
import { FlightSearchBarComponent } from "../../components/flight-search-bar/flight-search-bar.component/flight-search-bar.component";
import { CommonModule, NgOptimizedImage } from '@angular/common';

@Component({
  selector: 'app-home',
  imports: [FlightSearchBarComponent, CommonModule,
    NgOptimizedImage],
  templateUrl: './home.html',
  styleUrl: './home.scss'
})
export class Home {


  

}
