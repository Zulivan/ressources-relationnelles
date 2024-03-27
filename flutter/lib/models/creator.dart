class Creator {
  final int id;
  final String prenom;
  final String nom;

  Creator({
    required this.id,
    required this.prenom,
    required this.nom,
  });

  factory Creator.fromJson(Map<String, dynamic> json) {
    // print(json);
    return Creator(
      id: json['id'],
      prenom: json['prenom'],
      nom: json['nom'],
    );
  }
}