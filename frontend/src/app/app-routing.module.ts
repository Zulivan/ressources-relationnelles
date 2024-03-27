import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { RolesComponent } from './composants/roles/roles.component';
import { LoginComponent } from './composants/login/login.component';
import { HomeComponent } from './composants/home/home.component';
import { SignUpComponent } from './composants/sign-up/sign-up.component';
import { RessourcesComponent } from './composants/ressources/ressources.component';
import { CreateRessourcesComponent } from './composants/create-ressources/create-ressources.component';
import { RessourceComponent } from './composants/ressource/ressource.component';
import { ProfileComponent } from './composants/profile/profile.component';
import { StatistiquesComponent } from './composants/statistiques/statistiques.component';

const routes: Routes = [
  { path: '', component: HomeComponent },
  { path: 'roles', component: RolesComponent },
  { path: 'login', component: LoginComponent },
  { path: 'sign-up', component: SignUpComponent },
  { path: 'ressources', component: RessourcesComponent },
  { path: 'statistiques', component: StatistiquesComponent },
  { path: 'profil', component:ProfileComponent},
  { path: 'publier-une-ressource', component: CreateRessourcesComponent },
  { path: 'ressource/:id', component: RessourceComponent },
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule { }
