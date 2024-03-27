export interface CategoriesModel {
  'hydra:member': Member[];
  'hydra:totalItems': string;
}

export interface Member {
  id: number;
  libelle: string;
  lien: string;
  titre: string;
  texte: string;
  dateCreation: Date;
  typeRessource: any;
}
