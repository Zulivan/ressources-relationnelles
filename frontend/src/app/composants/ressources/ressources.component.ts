import { Component, OnInit } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { AppModule } from 'src/app/app.module';
import { map, Observable } from 'rxjs';
import { Member, CategoriesModel } from 'src/app/shared/classes/categories-model';
import { type } from 'jquery';

@Component({
  selector: 'app-ressources',
  templateUrl: './ressources.component.html',
  styleUrls: ['./ressources.component.scss']
})
export class RessourcesComponent implements OnInit {
  
  constructor(private http: HttpClient, private appModule: AppModule) { }

  categorieSelectionnee: string = '';
  typeSelectionne: string = '';
  validee: boolean = true;
  page = 1;

  user: any = null;

  categories!: Member[];
  typesRessources!: Member[];

  ressources!: Member[];

  public getCategories(): any {
    const url = 'api/categories';
    
    this.appModule.request(url).subscribe((data: any) => 
     {
      this.categories = data['hydra:member'] as Member[];
     });
  }

  public async getUser() {
    const user = await this.appModule.getUser();
    this.user = user;
  }

  public getTypesRessources(): any {
    const url = 'api/type_ressources';

    this.appModule.request(url).subscribe((data: any) =>
    {
      this.typesRessources = data['hydra:member']
    });
  }

  onSelectChange(value: any) {
    this.getRessources();
  }

  public getRessources(): any {
    const url = 'api/ressources?page='+this.page+'&categories.id='+this.categorieSelectionnee+'&typeRessource.id='+this.typeSelectionne+'&validee='+this.validee;

    this.appModule.request(url).subscribe((data: any) =>
    {
      this.ressources = data['hydra:member'] as Member[];
    });
  }

  ngOnInit(): void {
    this.getUser()
    this.getCategories()
    this.getTypesRessources()
    this.getRessources()
  }
}
