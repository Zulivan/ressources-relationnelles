class Commentaire {
  final int id;
  final String text;
  final Utilisateur utilisateur;
  final List<Commentaire> reponses;

  Commentaire({
    required this.id,
    required this.text,
    required this.utilisateur,
    required this.reponses,
  });

  factory Commentaire.fromJson(Map<String, dynamic> json) {
    // print(json);
    return Commentaire(
      id: json['id'],
      text: json['text'],
      utilisateur: Utilisateur.fromJson(json['utilisateur']),
    reponses: parseCommentaires(json['reponses']),
    );
  }

  static List<Commentaire> parseCommentaires(List<dynamic> commentairesJson) {
    return commentairesJson.map((json) => Commentaire.fromJson(json)).toList();
  }
}

// Utilisateur pour les commentaires
class Utilisateur {
  final int id;
  final String prenom;
  final String nom;
  // Add other properties as needed

  Utilisateur({
    required this.id,
    required this.prenom,
    required this.nom,
    // Add other constructor parameters as needed
  });

  factory Utilisateur.fromJson(Map<String, dynamic> json) {
    return Utilisateur(
      id: json['id'],
      prenom: json['prenom'],
      nom: json['nom'],
      // Parse and assign other properties from the JSON
    );
  }
}