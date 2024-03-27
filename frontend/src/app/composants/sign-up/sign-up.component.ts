import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { HttpClient, HttpHeaders } from '@angular/common/http';

@Component({
  selector: 'app-inscription',
  templateUrl: './sign-up.component.html',
  styleUrls: ['./sign-up.component.scss']
})
export class SignUpComponent implements OnInit {
  signupForm!: FormGroup;
  error: string = '';

  constructor(private fb: FormBuilder, private http: HttpClient) { }

  ngOnInit() {
    this.signupForm = this.fb.group({
      nom: ['', Validators.required],
      prenom: ['', Validators.required],
      date_naissance:['',Validators.required],
      email: ['', [Validators.required, Validators.email]],
      adresse: ['', Validators.required],
      ville: ['', Validators.required],
      code_postal: ['', Validators.required],
      password: ['', Validators.required]
    });
  }

  signup() {
    const url = 'http://localhost:8000/api/signup';
    const body = {
      nom: this.signupForm.value.nom,
      prenom: this.signupForm.value.prenom,
      date_naissance:this.signupForm.value.date_naissance,
      email: this.signupForm.value.email,
      adresse: this.signupForm.value.adresse,
      ville: this.signupForm.value.ville,
      code_postal: this.signupForm.value.code_postal,
      password: this.signupForm.value.password
    };
    const headers = {'Content-Type': 'application/json'};
    return this.http.post(url, body, {headers: headers});
  }

  onSubmit() {
    this.signup().subscribe({
      next: (data:any) => {
        if(data.token) {
          localStorage.setItem('token', data.token);
        }
        if(data.message) {
          this.error = data.message;
        }
      },
      error: (error) => {
        this.error = error.error.message;
      }
    });
  }
}
