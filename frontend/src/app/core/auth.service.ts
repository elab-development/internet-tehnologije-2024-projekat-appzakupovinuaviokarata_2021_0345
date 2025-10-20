import { inject, Injectable } from '@angular/core';
import { ApiService } from './api.service';
import { tap } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class AuthService {
  

  private api = inject(ApiService);
  private key = 'token';

  get token() { return localStorage.getItem(this.key); }
  set token(v: string | null) { v ? localStorage.setItem(this.key, v) : localStorage.removeItem(this.key); }

  login(email: string, password: string) {
    return this.api.post<{token:string,user:any}>('/auth/login', { email, password })
      .pipe(tap(res => this.token = res.token));
  }
  register(name: string, email: string, password: string) {
    return this.api.post<{token:string,user:any}>('/auth/register', { name, email, password })
      .pipe(tap(res => this.token = res.token));
  }
  logout() { this.token = null; return this.api.post('/auth/logout', {}); }
}
