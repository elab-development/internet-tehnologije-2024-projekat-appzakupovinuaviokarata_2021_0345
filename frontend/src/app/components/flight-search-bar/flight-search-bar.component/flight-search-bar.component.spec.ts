import { ComponentFixture, TestBed } from '@angular/core/testing';

import { FlightSearchBarComponent } from './flight-search-bar.component';

describe('FlightSearchBarComponent', () => {
  let component: FlightSearchBarComponent;
  let fixture: ComponentFixture<FlightSearchBarComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [FlightSearchBarComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(FlightSearchBarComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
