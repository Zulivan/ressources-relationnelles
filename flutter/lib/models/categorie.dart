class Categorie {
  final int id;
  final String libelle;

  Categorie({
    required this.id,
    required this.libelle,
  });

  factory Categorie.fromJson(Map<String, dynamic> json) {

    // print(json);
    
    return Categorie(
      id: json['id'],
      libelle: json['libelle'],
    );
  }
}