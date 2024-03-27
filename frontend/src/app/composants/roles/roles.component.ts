import { Component, OnInit } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { map, Observable } from 'rxjs';
import { Member, RoleModel } from 'src/app/shared/classes/role-model';

@Component({
  selector: 'app-roles',
  templateUrl: './roles.component.html',
  styleUrls: ['./roles.component.scss']
})
export class RolesComponent implements OnInit {

  roles: RoleModel[] = [];
  dat!: RoleModel;
  mem!: Member[];

  constructor(private http: HttpClient) {}

  private baseUrl = 'http://127.0.0.1:8000/api';

  public getRoles(): any {
     this.http.get<RoleModel>(`${this.baseUrl}/roles?page=1`).subscribe((data) => 
     {
      this.dat = data
      this.mem = this.dat['hydra:member'];
     });
  }

  ngOnInit(): void {
    this.getRoles()
  }
}


