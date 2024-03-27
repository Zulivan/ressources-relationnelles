import { Component, OnInit, Inject } from '@angular/core';
import { AppModule } from 'src/app/app.module';
import { ActivatedRoute } from '@angular/router';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { DOCUMENT } from '@angular/common';
import { DomSanitizer } from '@angular/platform-browser';

@Component({
  selector: 'app-ressource',
  templateUrl: './ressource.component.html',
  styleUrls: ['./ressource.component.scss']
})


export class RessourceComponent implements OnInit  {
  ressource: any = {
    createur: {
      '@id' : '',
        nom: '',
       prenom: '',
    },
    categories: [],
    commentaires: [],
    dateCreation: '',
  }
  commentForm!: FormGroup;
  ressourceId = this.route.snapshot.paramMap.get('id');
  message: string = '';
  user: any = null;
  commentLabel: string = 'Commenter sous cette ressource';

  constructor(private appModule: AppModule,  private route: ActivatedRoute, private fb: FormBuilder, @Inject(DOCUMENT) private document: Document, private sanitizer: DomSanitizer) { }

  commenter() {
    const url = 'api/ressource_comment';
    const body = {
      message: this.commentForm.value.message,
      ressource: this.ressourceId,
      reponse: this.commentForm.value.reponse,
    };
    return this.appModule.request(url, 'POST', body);
  }

  validerRessource() {
    const url = 'api/ressources/'+this.ressourceId+'/valider';
    this.appModule.request(url, 'PUT').subscribe({
      next: (data:any) => {
        this.getRessource();
      }
    });
  }

  supprimerCommentaire(commentaire: any) {
    const url = 'api/ressource_comment/'+commentaire.id;
    this.appModule.request(url, 'DELETE').subscribe({
      next: (data:any) => {
        this.getRessource();
      }
    });
  }

  scrollTo(id: string) {
    const element = this.document.getElementById(id);
    if (element) {
      element.scrollIntoView({ behavior: 'smooth' });
    }
  }

  onSubmit() {
    this.commenter().subscribe({
      next:  (data:any) => {
        this.getRessource('commentaire-'+data.id);
        this.commentForm.reset();
        if(data.message) {
          this.message = data.message;
        }
      },
      error: (error: any) => {
        this.message = error.error.message;
      }
    });
  }

  async getRessource(scrollTo: any = null) {
    const id = this.ressourceId;
    const url = 'api/ressources/'+id;

    await this.appModule.request(url, 'GET').subscribe({
      next: (data:any) => {
        this.ressource = {...data,
          safe: this.sanitizer.bypassSecurityTrustUrl(data.lien),
        };

        setTimeout(() => {
          this.scrollTo(scrollTo);
        }, 2);
      },
      error: (error) => {
        console.log(error)
      }
    });
  }

  public async getNom() {
    const user = await this.appModule.getUser();
    this.user = user;
  }

  setReponse(reponse: any) {
    this.commentLabel = 'Répondre au commentaire de '+reponse.utilisateur.prenom+' '+reponse.utilisateur.nom;
    this.commentForm.patchValue({
      reponse: reponse.id
    });
    this.scrollTo('publierCommentaire');
  }

  ngOnInit() {
    this.getNom();
    this.commentForm = this.fb.group({
      message: ['', Validators.required],
      reponse: [null]
    });
    this.route.paramMap.subscribe( paramMap => {
        this.ressourceId = paramMap.get('id');
    })
    this.getRessource();
  }

}
