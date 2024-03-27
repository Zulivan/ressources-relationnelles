import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import {Router} from "@angular/router"

//get request function from app.module.ts
import { AppModule } from 'src/app/app.module';

@Component({
  selector: 'app-login',
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.scss']
})

export class LoginComponent implements OnInit {
  loginForm!: FormGroup;
  error: string = '';

  constructor(private fb: FormBuilder, private http: HttpClient, private router: Router, private appModule: AppModule) { }

  ngOnInit() {
    this.loginForm = this.fb.group({
      email: ['', [Validators.required, Validators.email]],
      password: ['', Validators.required]
    });
  }

  login() {
    const url = 'api/login_check';
    const body = {
      username: this.loginForm.value.email,
      password: this.loginForm.value.password
    };
    
    return this.appModule.request(url, 'POST', body);
  }

  onSubmit() {
    this.login().subscribe({
      next: (data:any) => {
        if(data.token) {
          localStorage.setItem('token', data.token);
          this.error = 'Connexion réussie';
          // redirect to home page
          document.location.href = '/';
          // this.router.navigate(['/'])


        }
        if(data.message) {
          this.error = data.message;
        }
      },
      error: (error) => {
        console.log(error)
        this.error = error.error.message;
      }
    });
  }
}
