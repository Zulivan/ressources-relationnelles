import { Component } from '@angular/core';
import { AppModule } from 'src/app/app.module';

@Component({
  selector: 'app-navbar',
  templateUrl: './navbar.component.html',
  styleUrls: ['./navbar.component.scss']
})
export class NavbarComponent {

  public isCollapsed = true;

  user: any = null;
  nom: string = '';

  constructor(private appModule: AppModule) { 
    this.getNom();
  }

  public async getNom() {
    const user = await this.appModule.getUser();
    this.user = user;
    this.nom = user ? user.nom : '';
  }
  
  logout(){
    localStorage.removeItem('token');
    document.location.href = '/';
  }
}
