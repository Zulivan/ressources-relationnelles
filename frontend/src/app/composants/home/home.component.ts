import { Component, OnInit } from '@angular/core';
import { AppModule } from 'src/app/app.module';

@Component({
  selector: 'app-home',
  templateUrl: './home.component.html',
  styleUrls: ['./home.component.scss']
})
export class HomeComponent implements OnInit {
  ressources: any = [];
  favoris: any = [];
  nom: string = '';
  page: number = 1;

  ngOnInit(): void {
  }

  constructor(private appModule: AppModule) {
    this.listeRessources().subscribe({
      next: (data:any) => {
        this.ressources = data['hydra:member']
        console.log(this.ressources);          
      },
      error: (error) => {
        console.log(error)
      }
    });


    this.getNom();

    this.listeFavoris();
    
  }

  public async getNom() {
    const user = await this.appModule.getUser();
    this.nom = user ? user.nom : '';
  }


  listeRessources(page = 1) {
    this.page = page;
    const url = 'api/ressources?order[dateCreation]=desc&page='+page;
    
    return this.appModule.request(url, 'GET');
  }

  chargerPlusRessources() {
    this.listeRessources(this.page+1).subscribe({
      next: (data:any) => {
        this.ressources = [...this.ressources, ...data['hydra:member']];
        this.page++;
      },
      error: (error) => {
        console.log(error)
      }
    });
  }

  dansFavoris(ressource: any) {
    return this.favoris.find((favori: any) => favori.id === ressource.id);
  }

  mettreFavori(ressource: any) {
    const url = 'api/ressources/'+ressource.id+'/favori';

    this.appModule.request(url, 'POST').subscribe({
      next: (data:any) => {
        this.listeFavoris();
      },
      error: (error) => {
        console.log(error)
      }
    });
  }
  async listeFavoris() {
    const user = await this.appModule.getUser();
    const url = 'api/utilisateurs/'+user.id;
    
    this.appModule.request(url, 'GET').subscribe({
      next: (data:any) => {
        this.favoris = data.favoris;
        console.log(this.favoris);
      }
    });
  }

}