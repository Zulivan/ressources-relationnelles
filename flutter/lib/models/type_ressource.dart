class TypeRessource {
  final int id;
  final String libelle;

  TypeRessource({
    required this.id,
    required this.libelle,
  });

  factory TypeRessource.fromJson(Map<String, dynamic> json) {
    // print(json);
    return TypeRessource(
      id: json['id'],
      libelle: json['libelle'],
    );
  }
}