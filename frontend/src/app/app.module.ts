import { NgModule } from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';
import { HttpClientModule } from '@angular/common/http';
import { ReactiveFormsModule } from '@angular/forms';

import { catchError, lastValueFrom, of } from 'rxjs';
import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';
import { FontAwesomeModule } from '@fortawesome/angular-fontawesome';
import { RolesComponent } from './composants/roles/roles.component';
import { LoginComponent } from './composants/login/login.component';
import { HomeComponent } from './composants/home/home.component';
import { SignUpComponent } from './composants/sign-up/sign-up.component';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { RessourcesComponent } from './composants/ressources/ressources.component'; 
import { NavbarComponent } from './composants/navbar/navbar.component';
import { NgbModule } from '@ng-bootstrap/ng-bootstrap';
import {NgbCollapseModule} from '@ng-bootstrap/ng-bootstrap';
import { FormsModule } from '@angular/forms';
import { CreateRessourcesComponent } from './composants/create-ressources/create-ressources.component';
import { RessourceComponent } from './composants/ressource/ressource.component';
import { ProfileComponent } from './composants/profile/profile.component';
import { CommonModule } from '@angular/common';
import { StatistiquesComponent } from './composants/statistiques/statistiques.component';


@NgModule({
  declarations: [
    AppComponent,
    RolesComponent,
    LoginComponent,
    HomeComponent,
    SignUpComponent,
    RessourcesComponent,
    RessourceComponent,
    ProfileComponent,
    NavbarComponent,
    CreateRessourcesComponent,
    StatistiquesComponent,
  ],
  imports: [
    CommonModule,
    BrowserModule,
    AppRoutingModule,
    HttpClientModule,
    ReactiveFormsModule,
    FontAwesomeModule,
    NgbModule,
    FormsModule,
  ],
  providers: [],
  bootstrap: [AppComponent]
})
export class AppModule {
  constructor(private http: HttpClient) { }
  user: any = null;

  public async isLoggedIn(): Promise<boolean> {
    const a = await this.getUser();
    let token = localStorage.getItem('token');
    
    return token ? true : false;
  }
  
  public request(url: string, method: string = "GET", body: any = null, headers: any = {}) {
      let token = localStorage.getItem('token');

      if (url[0] == '/') {
        url = url.substr(1);
      }
      
      if (!url.startsWith('http')) {
        url = 'http://localhost:8000/' + url;
      }

      if(!headers['Content-Type'])headers['Content-Type'] = 'application/json';
      if(headers['Content-Type'] == "null"){
        delete headers['Content-Type'];
      }

      if (token) {
        headers['Authorization'] = 'Bearer ' + token;
      }

      headers = new HttpHeaders(headers);

      method = method.toUpperCase();
      
      return this.http.request(method, url, {body, headers});
  }

  public async getUser() {
    if (this.user) {
      return this.user;
    } else if(localStorage.getItem('token')) {
      this.user = JSON.parse(localStorage.getItem('user') || '{}');
      const data = await lastValueFrom(this.request('api/citoyen', 'GET').pipe(
        catchError((error) => {
          if(error.status == 401){
            localStorage.removeItem('token');
            localStorage.removeItem('user');
            document.location.href = '/';
          }
          return of(null);
        })
      ));
      if (data) {
        this.user = data;
        localStorage.setItem('user', JSON.stringify(this.user));
      }
    }else{
      localStorage.removeItem('user');
    }
    return this.user
  }
}
