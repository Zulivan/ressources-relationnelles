export interface TypesModel {
  'hydra:member': Member[];
  'hydra:totalItems': string;
}

export interface Member {
  id: number;
  libelle: string;
}
