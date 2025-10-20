import { Routes } from '@angular/router';
import { Home } from './pages/home/home';
import { Register } from './features/auth/register/register';
import { Search } from './features/flights/search/search';
import { Login } from './features/auth/login/login';
import { Results } from './features/flights/results/results';
import { List } from './features/bookings/list/list';


export const routes: Routes = [
  { path: '', component: Home },
  { path: 'auth/login', component: Login},
  { path: 'auth/register', component: Register },
  { path: 'flights', component: Search},
  { path: 'flights/results', component: Results },
  { path: 'bookings', component: List },
];
