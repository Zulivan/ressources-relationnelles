import '../models/creator.dart';
import '../models/type_ressource.dart';
import '../models/commentaire.dart';
import '../models/categorie.dart';

class Ressource {
  final int id;
  final String titre;
  final String texte;
  final String dateCreation;
  final Creator createur;
  final int nbFavoris;
  final int nbCommentaires;
  final TypeRessource typeRessource;
  final bool validee;
  final List<Commentaire> commentaires;
  final List<Categorie> categories;
  final String? document;

  Ressource({
    required this.id,
    required this.titre,
    required this.texte,
    required this.dateCreation,
    required this.createur,
    required this.nbFavoris,
    required this.nbCommentaires,
    required this.typeRessource,
    required this.validee,
    required this.commentaires,
    required this.categories,
    this.document,
  });

factory Ressource.fromJson(Map<String, dynamic> json) {
  return Ressource(
    id: json['id'],
    titre: json['titre'],
    texte: json['texte'],
    dateCreation: json['dateCreation'],
    createur: Creator.fromJson(json['createur']),
    nbFavoris: json['nbFavoris'],
    nbCommentaires: json['nbCommentaires'],
    typeRessource: TypeRessource.fromJson(json['typeRessource']),
    validee: json['validee'],
    commentaires: parseCommentaires(json['commentaires']),
    categories: parseCategories(json['categories']),
    document: json['document'],
  );
}

  static List<Commentaire> parseCommentaires(List<dynamic> commentairesJson) {
    // print(commentairesJson);
    return commentairesJson.map((json) => Commentaire.fromJson(json)).toList();
  }

  static List<Categorie> parseCategories(List<dynamic> categoriesJson) {
        // print(categoriesJson);
        return categoriesJson.map((json) => Categorie.fromJson(json)).toList();
  }
}