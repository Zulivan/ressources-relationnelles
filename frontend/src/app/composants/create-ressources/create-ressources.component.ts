import { Component,OnInit } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Member, CategoriesModel } from 'src/app/shared/classes/categories-model';
import { TypesModel } from 'src/app/shared/classes/types-model';
import { AppModule } from 'src/app/app.module';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';

@Component({
  selector: 'app-create-ressources',
  templateUrl: './create-ressources.component.html',
  styleUrls: ['./create-ressources.component.scss']
})
export class CreateRessourcesComponent implements OnInit {

  constructor(private http: HttpClient, private appModule: AppModule, private fb: FormBuilder) { }

  error: string = '';
  form!: FormGroup;
  roles: Member[] = [];
  categories!: Member[];
  types!: Member[];
  fichier!: File;

  ngOnInit(): void {
    this.form = this.fb.group({
      titre: ['', [Validators.required]],
      contenu: ['', Validators.required],
      categorie: ['', Validators.required],
      type: ['', Validators.required],
      fichier: [''],
      lien: ['']
    });

    this.getCategories()
    this.getTypes()

  }

  public getCategories(): any {
    const url = 'api/categories?page=1';
    
    this.appModule.request(url).subscribe((data: any) => 
     {
      this.categories = data['hydra:member'] as Member[];
     });
  }


  public getTypes(): any {
    const url = 'api/type_ressources?page=1';
    
    this.appModule.request(url).subscribe((data: any) =>
      {
        this.types = data['hydra:member'] as Member[];
      });
  }

    typeRessource(valide: string){
      if(!this.types) return false;
      const type = this.types.find((type) => type.id == this.form.value.type);

      if(type) {
        if(type.libelle.toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "").includes(valide.toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, ""))) {
          return true;
        }
      }
      return false;
    }

    ajoute() {
      // const file = this.form.value.fichier;
      // const reader = new FileReader();
      // reader.readAsDataURL(file);
      // reader.onload = () => {
      //   this.form.value.fichier = reader.result;
      //   console.log(reader.result);
      // };

      const url = 'api/ajout_ressource';
      const formData = new FormData();
      formData.append('titre', this.form.value.titre);
      formData.append('contenu', this.form.value.contenu);
      formData.append('categorie', this.form.value.categorie);
      formData.append('type', this.form.value.type);
      formData.append('lien', this.form.value.lien);

      if(this.fichier)formData.append('fichier', this.fichier, this.fichier.name);

      return this.appModule.request(url, 'POST', formData, {
        "Content-Type": "null"
      });
    }
  
    public onFileChanged(event: any) {
      if (event.target.files && event.target.files.length) {
        const file = event.target.files[0];
        this.fichier = new File([file], file.name, { type: file.type });
      }
    }

    onSubmit() {
      this.ajoute().subscribe({
        next: (data:any) => {
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
